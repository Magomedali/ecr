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

class Objects extends ActiveRecordVersionable 
{
    

    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%object}}';
    }



    public static function versionableAttributes(){
        return [
            'guid',
            'name',
            'boundary_id',
            'boundary_guid',
            'isDeleted',
        ];
    }

    


	public function rules(){
		return [
            // name, email, subject and body are required
            [['name','guid','boundary_id','boundary_guid'], 'required'],
            ['guid','unique','targetClass' => '\common\models\Objects', 'message' => 'Запись с таким guid уже существует!'],
            [['name'], 'filter','filter'=>function($v){return trim(strip_tags($v));}],
            ['boundary_id','integer'],
            [['guid','boundary_guid'],'string','max'=>32],
            [['name'],'string','max'=>128]
            
        ];
	}


    
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
    	return array(
    		'id'=>'Id',
    		'guid'=>'Идентификатор в 1С',
            'name'=>'Наименование',
            'boundary_id'=>'Граница',
            'boundary_guid'=>'Граница'
    	);
    }




}