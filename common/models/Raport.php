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

class Raport extends ActiveRecordVersionable 
{
    

    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%raport}}';
    }



    public static function versionableAttributes(){
        return [
            'username',
            'email',
            'auth_key',
            'password_hash',
            'password_reset_token',
            'status',
            'phone',
            'name',
            'isDeleted',
        ];
    }

    


	public function rules(){
		return [
            // name, email, subject and body are required
            [['name','email','phone','password'], 'required'],
            
            [['name','username','email','phone'], 'filter','filter'=>function($v){return trim(strip_tags($v));}],
            ['email','email'],
            ['username','default','value'=>null],
            
            ['password', 'string', 'min' => 6],
            [['password_hash','auth_key'],'safe'],
            ['email','unique','targetClass' => '\common\models\Seller', 'message' => 'Такой email уже используется'],
            ['phone','unique','targetClass' => '\common\models\Seller', 'message' => 'Такой телефон уже используется'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
	}


    
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
    	return array(
    		'id'=>'Id',
    		'name'=>'Название',
            'username'=>'Логин',
            'email'=>'E-mail',
            'phone'=>'Теленфон',
            'password'=>'Пароль'
    	);
    }




}