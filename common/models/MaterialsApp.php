<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;

use common\models\User;
use common\models\MaterialsAppItem;
use common\models\Nomenclature;
use common\models\StockRoom;
use common\dictionaries\ExchangeStatuses;


use common\base\ActiveRecordVersionable;

class MaterialsApp extends ActiveRecordVersionable 
{
    
    protected $items = [];
    protected $itemsErrors = [];

    protected $user;



    protected $master;
    

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%materials_app}}';
    }


	public function rules(){
		return [
            // name, email, subject and body are required
            [['user_guid','master_guid','stockroom_guid'], 'required'],
            ['created_at','filter','filter'=>function($v){
                $date = $v ? date("Y-m-d\TH:i:s",strtotime($v)) : date("Y-m-d\TH:i:s",time());

                return $date;
            }],
            [['user_guid','stockroom_guid','master_guid'], 'string', 'max' => 36],
            ['created_at','default','value'=>date("Y-m-d\TH:i:s",time())],
            ['number','default','value'=>null],
            ['status', 'default', 'value' => ExchangeStatuses::CREATED],
            ['status', 'in', 'range' => [
                ExchangeStatuses::CREATED, 
                ExchangeStatuses::IN_CONFIRMING,
                ExchangeStatuses::CONFIRMED,
                ExchangeStatuses::DELETED]
            ],
        ];
	}

    public static function versionableAttributes(){
        return [
            'guid',
            'created_at',
            'number',
            'status',
            'user_guid',
            'master_guid',
            'stockroom_guid',
            'isDeleted',
        ];
    }

    public function load($data, $formName = null){
        
        if(parent::load($data, $formName)){
            
            //Проверяем есть ли гуид бригады в базе
            if($this->user_guid){

                $user = User::findOne(['guid'=>$this->user_guid]);

                if(!isset($user->id)){
                    $this->addError('user_guid',"user '".$this->user_guid."' not exists on the site");
                    return false;
                }else{
                    $this->user = $user;
                }
            }

            if($this->master_guid){
                $m = User::findOne(['guid'=>$this->master_guid,'is_master'=>1]);
                if(!isset($m->id)){
                    $this->addError('master_guid',"Master ".$this->master_guid." not exists on the site");
                    return false;
                }
            }

            if($this->stockroom_guid){

                $stockroom = StockRoom::findOne(['guid'=>$this->stockroom_guid]);

                if(!isset($stockroom->id)){
                    $this->addError('stockroom_guid',"Stockroom '".$this->stockroom_guid."' not exists on the site");
                    return false;
                }
            }

            $scope = $formName === null ? $this->formName() : $formName;
            
            if(isset($data[$scope]['items']) && is_array($data[$scope]['items'])){
                $this->items = $data[$scope]['items'];
            }elseif(isset($data['MaterialsAppItem']) && is_array($data['MaterialsAppItem'])){
                $this->items = $data['MaterialsAppItem'];
            }else{
                $this->items = [];
            }

            if(!count($this->items)){
                $this->addError('items',"doesn`t have nomenclatures");
                return false;
            }

            
            return true;
        }

        return false;
    }
    
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
    	return array(
    		'id'=>'Id',
    		'user_guid'=>'Ответственный',
            'master_guid'=>"Мастер",
            'stockroom_guid'=>'Склад',
            'created_at'=>'Дата создания документа',
            'number'=>'Номер документа',
            'status'=>'Статус'
    	);
    }


    public function getItemsErrors(){
        return $this->itemsErrors;
    }



    public function getUser(){
        if(!$this->user_guid) return null;

        if($this->user instanceof User && isset($this->user->id))
            return $this->user;

        $user = User::findOne(['guid'=>$this->user_guid]);
        if(isset($user->id)){
            $this->user = $user;
            return $this->user;
        }
    }

    public function getMaster(){
        if(!$this->master_guid) return null;

        if($this->master instanceof User && isset($this->master->id))
            return $this->user;

        $master = User::findOne(['guid'=>$this->master_guid,'is_master'=>1]);
        if(isset($master->id)){
            $this->master = $master;
            return $this->master;
        }
    }


    public function getStockroom(){
        return $this->hasOne(StockRoom::className(),["guid"=>'stockroom_guid']);
    }


    public function setStatus($status){
        if(is_numeric($status)){
            $labels = ExchangeStatuses::getLabels();
            $this->status = array_key_exists($status, $labels) ? $status : ExchangeStatuses::CREATED;
        }else{
            $code = ExchangeStatuses::getCode($status);
            $this->status = $code ? $code : ExchangeStatuses::CREATED;
        }
    }


    public function getStatusTitle(){
        $title = ExchangeStatuses::getLabels($this->status);

        return !is_array($title) ? $title : null;
    }



    public function getIsCanUpdate(){
        return $this->status != ExchangeStatuses::CONFIRMED;
    }



    public function getMaterialsAppItems(){
        if($this->id){

            return (new Query)->select(['mai.nomenclature_guid','n.name as nomenclature_name','mai.count','n.unit as nomenclature_unit'])
                                ->from(['mai'=>MaterialsAppItem::tableName()])
                                ->innerJoin(['n'=>Nomenclature::tableName()]," n.guid = mai.nomenclature_guid")
                                ->where(['mai.material_app_id'=>$this->id])
                                ->all();
        }else{
           return $this->items; 
        }
    }

    public function saveRelationEntities(){

        //Связываем остатки
        if($this->items && $this->id){
            try {
                $transaction = Yii::$app->db->beginTransaction();

                $this->deleteMaterialsAppItems();

                if($this->saveItems()){
                    $transaction->commit();

                    return true;
                }
                
                $transaction->rollBack();
                $this->delete();
                
            } catch (\Exception $e) {
                $transaction->rollBack();
                $this->delete();
            }
            //Конец транзакции
        }else{
            $this->delete();
        }
        
        return false;
    }








    public function saveItems($data = []){
        if(!$this->id) return false;

        $items = count($data) ? $data : $this->items;

        if(!is_array($items)){
            return false;
        }

        
        $Type = "MaterialsAppItem";
        if(!isset($items[$Type])){
            $items[$Type] = $items;
        }
        
        if(!array_key_exists(0, $items[$Type])){
            $items[$Type] =  [$items[$Type]];
        }
        
        foreach ($items[$Type] as $key => $mdata) {
            $model = new MaterialsAppItem();

            $arData = is_object($mdata) ? json_decode(json_encode($mdata),1) : $mdata;
            $arData['material_app_id'] = $this->id;

            if(!$model->load(['MaterialsAppItem'=>$arData]) || !$model->save(1)){
                $this->itemsErrors[$model->nomenclature_guid] = json_encode($model->getErrors());
            }
        }

        if(count($this->itemsErrors)){
            Yii::warning("Error when save items","MaterialsApp::saveItems");
            Yii::warning(json_encode($this->itemsErrors),"MaterialsApp");
            //Yii::$app->session->setFlash("warning","Произошла ошибка при сохранении остатков");
        }


        return !count($this->itemsErrors);
    }





    public function deleteMaterialsAppItems($data = []){
        if(!$this->id) return false;

        Yii::$app->db->createCommand()->delete(MaterialsAppItem::tableName(),['material_app_id'=>$this->id])->execute();
    }

}