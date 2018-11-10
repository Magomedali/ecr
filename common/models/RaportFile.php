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


class RaportFile extends ActiveRecord 
{
    

    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%raport_files}}';
    }
    


	public function rules(){
		return [
            // name, email, subject and body are required
            [['raport_id','file_name','file_type','file'], 'required'],
            [['file_name','file_type','file'], 'filter','filter'=>function($v){return trim(strip_tags($v));}],
            ['creator_id','default','value'=>null],
            ['created_at','default','value'=>date("Y-m-d\TH:i:s",time())],
            [['created_at'], 'filter','filter'=>function($v){return $v ? date("Y-m-d\TH:i:s",time()) : null;}],
            ['file_type', 'string', 'min' => 32]
        ];
	}


    
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
    	return array(
    		'id'=>'Id',
    		'raport_id'=>'Рапорт',
            'created_at'=>'Дата',
            'file_name'=>'Имя файла',
            'file_type'=>'Тип файла',
            'file'=>'Файл',
            'creator_id'=>"Пользователь"
    	);
    }




}