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

class Nomenclature extends ActiveRecordVersionable 
{
    

    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%nomenclature}}';
    }



    public static function versionableAttributes(){
        return [
            'guid',
            'name',
            'isDeleted',
        ];
    }

    

    public function rules(){
        return [
            // name, email, subject and body are required
            [['guid','name'], 'required'],
            ['guid','unique','targetClass' => '\common\models\Nomenclature', 'message' => 'Запись с таким guid уже существует!'],
            [['name'],'string','max'=>128],
            [['guid'],'string','max'=>32],
            [['name'], 'filter','filter'=>function($v){return trim(strip_tags($v));}],
            
        ];
    }


    
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
        return array(
            'id'=>'Id',
            'guid'=>'Идентификатор в 1С',
            'name'=>'Наименование'
        );
    }




}