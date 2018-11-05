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


class RaportTechnic extends ActiveRecord 
{
    

    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%raport_technics}}';
    }
    


	public function rules(){
        return [
            // name, email, subject and body are required
            [['raport_id','technic_guid'], 'required'],
            ['raport_id','number'],
            ['technic_guid','string','max'=>32]
        ];
    }


    
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
        return array(
            'id'=>'Id',
            'raport_id'=>'Рапорт',
            'technic_guid'=>'Техника'
        );
    }




}