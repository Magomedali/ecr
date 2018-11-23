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
use common\models\Technic;

class RaportConsist extends ActiveRecord 
{
    

    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%raport_consist}}';
    }
    


	public function rules(){
        return [
            // name, email, subject and body are required
            [['raport_id','technic_guid','user_guid'], 'required'],
            ['raport_id','number'],
            [['technic_guid','user_guid'],'string','max'=>36]
        ];
    }


    
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
        return array(
            'id'=>'Id',
            'raport_id'=>'Рапорт',
            'technic_guid'=>'Техника',
            'user_guid'=>'Физ лицо'
        );
    }



    public function load($data, $formName = null){
        
        if(parent::load($data, $formName)){

            //Проверяем есть ли гуид номенклатуры в базе
            if($this->technic_guid){
                $m = Technic::findOne(['guid'=>$this->technic_guid]);
                if(!isset($m->id)){
                    $this->addError('technic_guid',"'".$this->technic_guid."' not exists on the site");
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


            $model = self::find()->where(['user_guid'=>$this->user_guid,'raport_id'=>$this->raport_id])->one();
            if ($model && isset($model->id)) {
                $this->id = $model->id;
                $this->setOldAttributes($model->attributes);           
            }

            return true;
        }

        return false;
    }

}