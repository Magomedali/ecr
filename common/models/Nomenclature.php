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
            'unit',
            'isDeleted',
        ];
    }

    

    public function rules(){
        return [
            // name, email, subject and body are required
            [['guid','name'], 'required'],
           // ['guid','unique','targetClass' => '\common\models\Nomenclature', 'message' => 'Запись с таким guid уже существует!'],
            [['name','unit'],'string','max'=>255],
            ['unit','default','value'=>null],
            [['guid'],'string','max'=>36],
            [['name','unit'], 'filter','filter'=>function($v){return trim(strip_tags($v));}],
            
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
            'unit'=>'Едининца измерения'
        );
    }




}