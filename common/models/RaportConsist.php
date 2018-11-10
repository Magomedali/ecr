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
            [['technic_guid','user_guid'],'string','max'=>32]
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




}