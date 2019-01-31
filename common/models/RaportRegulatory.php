<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;


use common\models\RaportRequlatoryWork;
use common\models\User;
use common\models\TypeOfWork;
use common\models\Brigade;

use common\base\ActiveRecordVersionable;
use common\dictionaries\ExchangeStatuses;



class RaportRegulatory extends ActiveRecordVersionable 
{
    
    protected  $works = [];
    protected  $worksErrors = [];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%raport_regulatory}}';
    }



    public static function versionableAttributes(){
        return [
            'guid',
            'number',
            'status',
            'created_at',
            'starttime',
            'endtime',
            
            'brigade_guid',
            // 'object_guid',
            // 'boundary_guid',
            // 'project_guid',
            'user_guid',
            'master_guid',
            'comment',

            'isDeleted',
        ];
    }

    


	public function rules(){
		return [
            // name, email, subject and body are required
            [['master_guid','user_guid','brigade_guid','created_at'], 'required','message'=>'Обязательное поле'],
            
            [['number','comment'], 'filter','filter'=>function($v){return trim(strip_tags($v));}],
            
            [['created_at'],'filter','filter'=>function($v){ return $v ? date("Y-m-d\TH:i:s",strtotime($v)) : date("Y-m-d\TH:i:s");}],

            [['starttime','endtime'],'filter','filter'=>function($v){ return $v ? date("H:i:s",strtotime($v)) : date("H:i:s");}],


            [['starttime','endtime'],'required','message'=>''],

            [['guid','master_guid','user_guid','brigade_guid'],'string','max'=>36],
            
            ['number', 'string', 'max' => 255],
            
            ['status','integer'],
            ['status', 'default', 'value' => ExchangeStatuses::CREATED],
            ['status', 'in', 'range' => [
                ExchangeStatuses::CREATED, 
                ExchangeStatuses::IN_CONFIRMING,
                ExchangeStatuses::CONFIRMED,
                ExchangeStatuses::DELETED]
            ],
        ];
	}



    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
        return array(
            'id'=>'Id',
            'guid'=>'Идентификатор 1С',
            'number'=>'Номер',
            'status'=>'Статус',
            'created_at'=>'Дата',
            'starttime'=>'Время начало работ',
            'endtime'=>'Время окончания работ',
            'brigade_guid'=>'Бригада',
            'user_guid'=>'Бригадир',
            'master_guid'=>"Мастер",
            'comment'=>"Комментарии",
            'isDeleted'=>"Удалена",
        );
    }




    public function load($data, $formName = null){
        
        if(parent::load($data, $formName)){

            if(is_numeric($this->status)){
                $labels = ExchangeStatuses::getLabels();
                $this->status = array_key_exists($this->status, $labels) ? $this->status : ExchangeStatuses::CREATED;
            }else{
                $code = ExchangeStatuses::getCode($this->status);
                $this->status = $code ? $code : ExchangeStatuses::CREATED;
            }
            

            //Проверяем есть ли гуид бригады в базе
            if($this->brigade_guid){
                $br = Brigade::findOne(['guid'=>$this->brigade_guid]);
                if(!isset($br->id)){
                    $this->addError('brigade_guid',"'".$this->brigade_guid."' not exists on the site");
                    return false;
                }
            }

            
            if($this->master_guid){
                $m = User::findOne(['guid'=>$this->master_guid,'is_master'=>1]);
                if(!isset($m->id)){
                    $this->addError('master_guid',"Master ".$this->master_guid." not exists on the site");
                    return false;
                }
            }

            if($this->user_guid){
                $u = User::findOne(['guid'=>$this->user_guid,'is_master'=>0]);
                if(!isset($u->id)){
                    $this->addError('user_guid',"User ".$this->user_guid." not exists on the site");
                    return false;
                }
            }

            if(!isset($this->id) && $this->guid){
                $model = self::find()->where(['guid'=>$this->guid])->one();
                if ($model && isset($model->id)) {
                    $this->id = $model->id;
                    $this->setOldAttributes($model->attributes);           
                } 
            }
            

            $scope = $formName === null ? $this->formName() : $formName;
            
            
            if(isset($data[$scope]['works']) && is_array($data[$scope]['works'])){
                $this->works = $data[$scope]['works'];
            }elseif(isset($data['RaportRequlatoryWork']) && is_array($data['RaportRequlatoryWork'])){
                $this->works = $data['RaportRequlatoryWork'];
            }else{
                $this->works = [];
            }

            if(!count($this->works)){
                $this->addError('works',"doesn`t have works");
                return false;
            }
            

            return true;
        }

        return false;
    }

    


    public function getMaster(){
        return $this->hasOne(User::className(),["guid"=>'master_guid']);
    }

    public function getBrigadier(){
        return $this->hasOne(User::className(),["guid"=>'user_guid']);
    }


    public function getStatusTitle(){
        $title = ExchangeStatuses::getLabels($this->status);

        return !is_array($title) ? $title : null;
    }

    public function getIsCanUpdate(){
        return $this->status <= ExchangeStatuses::IN_CONFIRMING;
    }

    
    public function getWorksErrors(){
        return $this->worksErrors;
    }




    public function getWorks(){
        if($this->id){
            return (new Query)->select(['rw.line_guid','u.name as user_name','rw.work_guid','tw.name as work_name','rw.count'])->from(['rw'=>RaportRequlatoryWork::tableName()])
                                ->innerJoin(['tw'=>TypeOfWork::tableName()]," tw.guid = rw.work_guid")
                                ->innerJoin(['u'=>User::tableName()]," u.guid = rw.user_guid")
                                ->where(['raport_regulatory_id'=>$this->id])
                                ->all();
        }else{
           return $this->works; 
        }
    }




    public function saveRelationEntities(){
        //Связываем материалы
        

        if($this->works && $this->id){
            try {
                $transaction = Yii::$app->db->beginTransaction();

                $this->deleteWorks();

                if($this->saveWorks()){
                    $transaction->commit();
                }else{
                    $transaction->rollBack();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
            }
        }else{
            //Если оъъектов нет удаляем из базы, если они есть
            $this->deleteWorks();
        }
    }






    public function saveWorks($data = []){
        if(!$this->id) return false;

        $works = count($data) ? $data : $this->works;
        if(!is_array($works)){
            return false;
        }
        
        $Type = "RaportRequlatoryWork";
        if(!isset($works[$Type])){
            $models[$Type] = $works;
        }else{
            $models = $works;
        }
        
        if(ArrayHelper::isAssociative($models[$Type])){
            $models[$Type] =  [$models[$Type]];
        }

        foreach ($models[$Type] as $key => $mdata) {
            $model = new RaportRequlatoryWork();

            $arData = is_object($mdata) ? json_decode(json_encode($mdata),1) : $mdata;
            $arData['raport_regulatory_id'] = $this->id;

            if(!$model->load(['RaportRequlatoryWork'=>$arData]) || !$model->save()){
                $this->worksErrors[$model->work_guid] = $model->getErrors();
            }
        }

        return !count($this->worksErrors);
    }


    public function deleteWorks($data = []){
        if(!$this->id) return false;

        Yii::$app->db->createCommand()->delete(RaportRequlatoryWork::tableName(),['raport_regulatory_id'=>$this->id])->execute();
    }




    public static function getUnconfirmedStatuses(){
        return [
            ExchangeStatuses::CREATED,
            ExchangeStatuses::IN_CONFIRMING
        ];
    }
}