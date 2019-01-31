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
use common\models\TypeOfWork;
use common\base\ActiveRecordVersionable;

class RaportRequlatoryWork extends ActiveRecordVersionable 
{
    

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%raport_regulatory_works}}';
    }



    public static function versionableAttributes(){
        return [
            'raport_regulatory_id',
            'work_guid',
            'user_guid',
            'count',
            'isDeleted',
        ];
    }

    


	public function rules(){
		return [
            // name, email, subject and body are required
            [['raport_regulatory_id','work_guid','user_guid','count'], 'required','message'=>'Обязательное поле'],
            
            [['count'], 'number'],

            [['count'], 'default','value'=>0],
            
            [['work_guid','user_guid'],'string','max'=>36],
            
            ['raport_regulatory_id', 'number','integerOnly'=>true],
        ];
	}


    
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
    	return array(
    		'id'=>'Id',
    		'raport_regulatory_id'=>'Рапорт регламентных работ',
            'work_guid'=>'Вид работы',
            'user_guid'=>'Физ лицо',
            'count'=>'Количество'
    	);
    }



    public function load($data, $formName = null){
        
        if(parent::load($data, $formName)){

            //Проверяем есть ли гуид номенклатуры в базе
            if($this->work_guid){
                $m = TypeOfWork::findOne(['guid'=>$this->work_guid]);
                if(!isset($m->id)){
                    $this->addError('work_guid',"'".$this->work_guid."' not exists on the site");
                    return false;
                }
            }

            if($this->user_guid){
                $m = User::findOne(['guid'=>$this->user_guid]);
                if(!isset($m->id)){
                    $this->addError('user_guid',"'".$this->user_guid."' not exists on the site");
                    return false;
                }
            }


            return true;
        }

        return false;
    }




}