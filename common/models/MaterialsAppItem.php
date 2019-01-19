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
use common\models\MaterialsApp;
use common\models\Nomenclature;

class MaterialsAppItem extends ActiveRecord 
{
    

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%materials_app_item}}';
    }
    


	public function rules(){
		return [
            [['material_app_id','nomenclature_guid','count'], 'required'],
            ['material_app_id','integer'],
            [['count'],'number'],
            [['nomenclature_guid'],'string','max'=>36],
        ];
	}

    public static function versionableAttributes(){
        return [
            'material_app_id',
            'nomenclature_guid',
            'count',
            'isDeleted',
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

            if(!isset($this->id) && $this->material_app_id && $this->nomenclature_guid){
                $model = self::find()->where(['material_app_id'=>$this->material_app_id,'nomenclature_guid'=>$this->nomenclature_guid])->one();
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
    		'material_app_id'=>'Заявка на материал',
            'nomenclature_guid'=>'Номенклатура',
            'count'=>'Количество'
    	);
    }




}