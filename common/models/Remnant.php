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

class Remnant extends ActiveRecordVersionable 
{
    

    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%remnant}}';
    }



    public static function versionableAttributes(){
        return [
            'brigade_guid',
            'updated_at',
            'nomenclature_guid',
            'count',
            'isDeleted',
        ];
    }

    


	public function rules(){
		return [
            // name, email, subject and body are required
            [['brigade_guid','nomenclature_guid','count'], 'required'],
            ['brigade_guid','unique','targetClass' => '\common\models\Remnant', 'message' => 'Запись с таким brigade_guid уже существует!'],
            ['updated_at','default','value'=>date("Y-m-d\TH:i:s",time())],
            [['count'],'number'],
            [['brigade_guid','nomenclature_guid'],'string','max'=>32],
        ];
	}


    
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
    	return array(
    		'id'=>'Id',
    		'brigade_guid'=>'Бригада',
            'nomenclature_guid'=>'Номенклатура',
            'updated_at'=>'Время обновления',
            'count'=>'Количество'
    	);
    }




}