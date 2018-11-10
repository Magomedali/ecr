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
            
            [['created_at','starttime','endtime'],'filter','filter'=>function($v){ return $v ? date("Y-m-d\TH:i:s",strtotime($v)) : null}],

            [['temperature_start','temperature_end','surface_temperature_start','surface_temperature_end','airhumidity_start','airhumidity_end'],'number'],

            [['guid','brigade_guid','object_guid','boundary_guid','project_guid','master_guid'],'string','max'=>32],
            
            ['number', 'string', 'max' => 255],

            ['guid','unique','targetClass' => '\common\models\Seller', 'message' => 'Такой рапорт уже создан'],
           
            ['status', 'default', 'value' => RaportStatuses::CREATED],
            ['status', 'in', 'range' => [
                RaportStatuses::CREATED, 
                RaportStatuses::IN_CONFIRMING,
                RaportStatuses::CONFIRMED,
                RaportStatuses::DELETED]
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