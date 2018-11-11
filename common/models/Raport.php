<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;

use common\models\User;
use common\models\Objects;
use common\models\Brigade;
use common\models\Project;

use common\base\ActiveRecordVersionable;
use common\dictionaries\RaportStatuses;


class Raport extends ActiveRecordVersionable 
{
    

    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%raport}}';
    }



    public static function versionableAttributes(){
        return [
            'guid',
            'number',
            'status',
            'created_at',
            'starttime',
            'endtime',
            'temperature_start',
            'temperature_end',
            'surface_temperature_start',
            'surface_temperature_end',
            'airhumidity_start',
            'airhumidity_end',
            'brigade_guid',
            'object_guid',
            'boundary_guid',
            'project_guid',
            'master_guid',
            'comment',

            'isDeleted',
        ];
    }

    


	public function rules(){
		return [
            // name, email, subject and body are required
            [['guid','brigade_guid','object_guid','boundary_guid','project_guid','master_guid','created_at'], 'required'],
            
            [['number','comment'], 'filter','filter'=>function($v){return trim(strip_tags($v));}],
            
            [['created_at'],'filter','filter'=>function($v){ return $v ? date("Y-m-d\TH:i:s",strtotime($v)) : date("Y-m-d\TH:i:s");}],

            [['starttime','endtime'],'filter','filter'=>function($v){ return $v ? date("H:i:s",strtotime($v)) : date("H:i:s");}],

            [['temperature_start','temperature_end','surface_temperature_start','surface_temperature_end','airhumidity_start','airhumidity_end'],'number'],

            [['guid','brigade_guid','object_guid','boundary_guid','project_guid','master_guid'],'string','max'=>32],
            
            ['number', 'string', 'max' => 255],
           
            ['status', 'default', 'value' => RaportStatuses::CREATED],
            ['status', 'in', 'range' => [
                RaportStatuses::CREATED, 
                RaportStatuses::IN_CONFIRMING,
                RaportStatuses::CONFIRMED,
                RaportStatuses::DELETED]
            ],
        ];
	}





    public function load($data, $formName = null){
        
        if(parent::load($data, $formName)){

            $code = RaportStatuses::getCode($this->status);
            $this->status = $code ? $code : null;
            
            //Проверяем есть ли гуид бригады в базе
            if($this->brigade_guid){
                $br = Brigade::findOne(['guid'=>$this->brigade_guid]);
                if(!isset($br->id)){
                    $this->addError('brigade_guid',"Бригада с таким guid отсутствует в базе");
                    return false;
                }
            }

            // //Проверяем есть ли гуид объекта в базе
            if($this->object_guid){
                $m = Objects::findOne(['guid'=>$this->object_guid]);
                if(!isset($m->id)){
                    $this->addError('object_guid',"Объект с таким guid отсутствует в базе");
                    return false;
                }
            }

            if($this->boundary_guid){
                $m = Boundary::findOne(['guid'=>$this->boundary_guid]);
                if(!isset($m->id)){
                    $this->addError('boundary_guid',"Граница с таким guid отсутствует в базе");
                    return false;
                }
            }

            if($this->project_guid){
                $m = Project::findOne(['guid'=>$this->project_guid]);
                if(!isset($m->id)){
                    $this->addError('project_guid',"Проект с таким guid отсутствует в базе");
                    return false;
                }
            }

            if($this->master_guid){
                $m = User::findOne(['guid'=>$this->master_guid,'is_master'=>1]);
                if(!isset($m->id)){
                    $this->addError('master_guid',"Мастер с таким guid отсутствует в базе");
                    return false;
                }
            }

            $model = self::find()->where(['guid'=>$this->guid])->one();
            if ($model && isset($model->id)) {
                $this->id = $model->id;
                $this->setOldAttributes($model->attributes);           
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
    		'guid'=>'Идентификатор 1С',
            'number'=>'Номер',
            'status'=>'Статус',
            'created_at'=>'Дата',
            'starttime'=>'Время начало работ',
            'endtime'=>'Время окончания работ',
            'temperature_start'=>"",
            'temperature_end'=>"",
            'surface_temperature_start'=>"",
            'surface_temperature_end'=>"",
            'airhumidity_start'=>"",
            'airhumidity_end'=>"",
            'brigade_guid'=>"Бригада",
            'object_guid'=>"Объект",
            'boundary_guid'=>"Округ",
            'project_guid'=>"Контракт",
            'master_guid'=>"Мастер",
            'comment'=>"Комментарии",
            'isDeleted'=>"Удалена",
    	);
    }




}