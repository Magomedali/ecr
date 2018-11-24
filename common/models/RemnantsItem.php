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
use common\models\Brigade;
use common\models\Nomenclature;

class RemnantsItem extends ActiveRecord 
{
    

    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%remnants_item}}';
    }
    


	public function rules(){
		return [
            
            [['package_id','nomenclature_guid','count'], 'required'],
            ['package_id','integer'],
            [['count'],'number'],
            [['nomenclature_guid'],'string','max'=>36],
        ];
	}



    public function load($data, $formName = null){
        
        if(parent::load($data, $formName)){
            //Проверяем есть ли гуид техники в базе
            if($this->nomenclature_guid){
                $m = Nomenclature::findOne(['guid'=>$this->nomenclature_guid]);
                if(!isset($m->id)){
                    $this->addError('nomenclature_guid',"nomenclature_guid ".$this->nomenclature_guid." not exists on the site");
                    return false;
                }
            }

            if(!isset($this->id) && $this->package_id && $this->nomenclature_guid){
                $model = self::find()->where(['package_id'=>$this->package_id,'nomenclature_guid'=>$this->nomenclature_guid])->one();
                if ($model && isset($model->id)) {
                    $this->id = $model->id;
                    $this->setOldAttributes($model->attributes);           
                } 
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
    		'package_id'=>'Package Id',
            'nomenclature_guid'=>'Номенклатура',
            'count'=>'Количество'
    	);
    }




}