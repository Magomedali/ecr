<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\Brigade;
use common\models\Nomenclature;

class RemnantsPackage extends ActiveRecord 
{
    
    protected $items = [];
    protected $itemsErrors = [];

    protected $user;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%remnants_package}}';
    }


    


	public function rules(){
		return [
            // name, email, subject and body are required
            [['user_guid'], 'required'],
            ['updated_at','filter','filter'=>function($v){
                $date = $v ? date("Y-m-d\TH:i:s",strtotime($v)) : date("Y-m-d\TH:i:s");
                return $date;
            }],
            ['updated_at','default','value'=>date("Y-m-d\TH:i:s",time())],
            
            [['isActual'],'default','value'=>1],
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
            $scope = $formName === null ? $this->formName() : $formName;
            
            
            if(isset($data[$scope]['items']) && is_array($data[$scope]['items'])){
                $this->items = $data[$scope]['items'];
            }elseif(isset($data[$scope]['RemnantsItem']) && is_array($data[$scope]['RemnantsItem'])){
                $this->items = $data[$scope]['RemnantsItem'];
            }else{
                $this->items = [];
            }

            if(!count($this->items)){
                $this->addError('items',"doesn`t have items");
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
    		'id'=>'Package Id',
    		'user_guid'=>'Ответственный',
            'updated_at'=>'Время обновления',
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



    public function savePackage(){
        $user = $this->getUser();
        if(!isset($user->id) || $this->hasErrors()) return false;

        $items = $this->items;
        $Type = "RemnantsItem";
        if(!isset($items[$Type])){
            $models[$Type] = $items;
        }else{
            $models = $items;
        }
        
        if(ArrayHelper::isAssociative($models[$Type])){
            $models[$Type] =  [$models[$Type]];
        }

        // Если актуальные остатки на сайте равны этим, то просто возвращаем true
        if($user->remnantItemsIsEqual($models[$Type])) return true;

        return $this->save() && $this->saveRelationEntities();
    }







    public function saveRelationEntities(){


        //Связываем остатки
        if($this->items && $this->id){
            try {
                $transaction = Yii::$app->db->beginTransaction();

                $this->doUnActialPackage();

                if($this->saveItems()){
                    $transaction->commit();

                    return true;
                }else{
                    $transaction->rollBack();
                    $this->delete();
                }
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

        
        
        $Type = "RemnantsItem";
        if(!isset($items[$Type])){
            $items[$Type] = $items;
        }
        
        if(!array_key_exists(0, $items[$Type])){
            $items[$Type] =  [$items[$Type]];
        }
        
        foreach ($items[$Type] as $key => $mdata) {
            $model = new RemnantsItem();

            $arData = is_object($mdata) ? json_decode(json_encode($mdata),1) : $mdata;
            $arData['package_id'] = $this->id;

            if(!$model->load(['RemnantsItem'=>$arData]) || !$model->save()){
                $this->itemsErrors[$model->nomenclature_guid] = json_encode($model->getErrors());
            }
        }

        return !count($this->itemsErrors);
    }





    public function doUnActialPackage(){
        if(!$this->id || !$this->user_guid) return false;
        return Yii::$app->db->createCommand()->update(self::tableName(),['isActual'=>0],"`isActual`=1 AND `user_guid`='{$this->user_guid}' AND `id` <> {$this->id}")
        ->execute();
    }

}