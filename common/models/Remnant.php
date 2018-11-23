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
            ['updated_at','filter','filter'=>function($v){
                $date = $v ? date("Y-m-d\TH:i:s",strtotime($v)) : date("Y-m-d\TH:i:s");
                return $date;
            }],
            ['updated_at','default','value'=>date("Y-m-d\TH:i:s",time())],
            [['count'],'number'],
            [['brigade_guid','nomenclature_guid'],'string','max'=>36],
        ];
	}



    public function load($data, $formName = null){
        
        if(parent::load($data, $formName)){

            
            //Проверяем есть ли гуид бригады в базе
            if($this->brigade_guid){

                $br = Brigade::findOne(['guid'=>$this->brigade_guid]);

                if(!isset($br->id)){
                    $this->addError('brigade_guid',"brigade_guid '".$this->brigade_guid."' not exists on the site");
                    return false;
                }
            }

            //Проверяем есть ли гуид техники в базе
            if($this->nomenclature_guid){
                $m = Nomenclature::findOne(['guid'=>$this->nomenclature_guid]);
                if(!isset($m->id)){
                    $this->addError('nomenclature_guid',"nomenclature_guid ".$this->nomenclature_guid." not exists on the site");
                    return false;
                }
            }

            

            $model = self::find()->where(['brigade_guid'=>$this->brigade_guid])->one();
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
    		'brigade_guid'=>'Бригада',
            'nomenclature_guid'=>'Номенклатура',
            'updated_at'=>'Время обновления',
            'count'=>'Количество'
    	);
    }




}