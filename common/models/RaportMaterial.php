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
use common\models\Nomenclature;
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
            'nomenclature_guid',
            'was',
            'spent',
            'rest',
            'isDeleted',
        ];
    }

    


	public function rules(){
		return [
            // name, email, subject and body are required
            [['raport_id','nomenclature_guid','spent'], 'required'],
            ['raport_id', 'number','integerOnly'=>true],
            [['nomenclature_guid'],'string','max'=>36],

            [['was','spent','rest'], 'number'],

            [['was','spent','rest'], 'default','value'=>0]
        ];
	}


    public function load($data, $formName = null){
        
        if(parent::load($data, $formName)){

            //Проверяем есть ли гуид номенклатуры в базе
            if($this->nomenclature_guid){
                $m = Nomenclature::findOne(['guid'=>$this->nomenclature_guid]);
                if(!isset($m->id)){
                    $this->addError('nomenclature_guid',"'".$this->nomenclature_guid."' not exists on the site");
                    return false;
                }
            }

            $model = self::find()->where(['nomenclature_guid'=>$this->nomenclature_guid,'raport_id'=>$this->raport_id])->one();
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
    		'raport_id'=>'Рапорт',
            'nomenclature_guid'=>'Номенклатура',
            'was'=>'Начальный остаток',
            'spent'=>'Израсходовано',
            'rest'=>'Исходный остаток'
    	);
    }




}