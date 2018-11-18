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
use common\models\Boundary;
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
            [['name'], 'filter','filter'=>function($v){return trim(strip_tags($v));}],
            ['boundary_id','integer'],
            [['guid','boundary_guid'],'string','max'=>32],
            [['name'],'string','max'=>128]
            
        ];
	}



    public function load($data, $formName = null){
        
        if(parent::load($data, $formName)){

            //Проверяем есть ли гуид техники в базе
            if($this->boundary_guid){
                $m = Boundary::findOne(['guid'=>$this->boundary_guid]);
                if(!isset($m->id)){
                    $this->addError('boundary_guid',"Округ с таким guid отсутствует в базе");
                    return false;
                }else{
                    $this->boundary_id = $m->id;
                }
            }

            $model = self::find()->where(['guid'=>$this->guid])->one();
            if($model && isset($model->id)){
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
            'boundary_id'=>'Граница',
            'boundary_guid'=>'Граница'
    	);
    }

    public function getBoundary(){
        return $this->hasOne(Boundary::className(),["guid"=>'boundary_guid']);
    }


}