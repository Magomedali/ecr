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

class RaportMaterial extends ActiveRecordVersionable 
{
    

    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%raport_materials}}';
    }



    public static function versionableAttributes(){
        return [
            'raport_id',
            'brigade_guid',
            'item_guid',
            'it_was',
            'spent',
            'rest',
            'isDeleted',
        ];
    }

    


	public function rules(){
		return [
            // name, email, subject and body are required
            [['raport_id','brigade_guid','item_guid','it_was','spent','rest'], 'required'],
            ['raport_id', 'number','integerOnly'=>true],
            [['brigade_guid','item_guid'],'string','max'=>32],

            [['it_was','spent','rest'], 'number'],

            [['it_was','spent','rest'], 'default','value'=>0]
        ];
	}


    
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
    	return array(
    		'id'=>'Id',
    		'raport_id'=>'Рапорт',
            'brigade_guid'=>'Бригада',
            'item_guid'=>'Номенклатура',
            'it_was'=>'Начальный остаток',
            'spent'=>'Израсходовано',
            'rest'=>'Исходный остаток'
    	);
    }




}