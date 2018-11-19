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

class Technic extends ActiveRecordVersionable 
{
    

    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%technic}}';
    }



    public static function versionableAttributes(){
        return [
            'guid',
            'name',
            'marka',
            'number',
            'isDeleted',
        ];
    }

    

    public function rules(){
        return [
            // name, email, subject and body are required
            [['guid','name','marka','number'], 'required','message'=>'is required'],
            [['guid'],'string','max'=>36,'message'=>'max length 36'],
            [['name','marka','number'],'string','max'=>128],
            [['name','marka','number'], 'filter','filter'=>function($v){return trim(strip_tags($v));}],
            
        ];
    }



    public function load($data, $formName = null){
        
        if(parent::load($data, $formName)){

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
            'guid'=>'Идентификатор в 1С',
            'name'=>'Наименование',
            "marka"=>"Марка",
            "number"=>"Номер"
        );
    }




}