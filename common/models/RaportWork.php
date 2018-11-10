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

class RaportWork extends ActiveRecordVersionable 
{
    

    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%raport_works}}';
    }



    public static function versionableAttributes(){
        return [
            'raport_id',
            'work_guid',
            'line_guid',
            'mechanized',
            'length',
            'count',
            'squaremeter',
            'isDeleted',
        ];
    }

    


	public function rules(){
		return [
            // name, email, subject and body are required
            [['raport_id','work_guid','line_guid','length','count'], 'required'],
            
            [['length','count','squaremeter'], 'number'],

            [['length','count','squaremeter'], 'default','value'=>0],
            
            [['work_guid','line_guid'],'string','max'=>32],
            
            ['mechanized','boolean'],
            ['mechanized','default','value'=>false],

            ['raport_id', 'number','integerOnly'=>true],
        ];
	}


    
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
    	return array(
    		'id'=>'Id',
    		'raport_id'=>'Рапорт',
            'work_guid'=>'Вид работы',
            'line_guid'=>'Линия',
            'length'=>'Длина',
            'count'=>'Количество',
            'mechanized'=>'Механизированная работа',
            'squaremeter'=>'Квадратура'
    	);
    }




}