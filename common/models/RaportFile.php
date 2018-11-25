<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;
use yii\db\ActiveRecord;
use common\models\User;


class RaportFile extends ActiveRecord 
{
    
    public $loadedFile;
    

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
            ['file_type', 'string', 'max' => 255],
            ['loadedFile','file','extensions'=>['png','gif','jpg','jpeg','pdf']]
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
            'file_binary'=>'Файл',
            'creator_id'=>"Пользователь"
    	);
    }



    public static function getImageTypes(){
        return ['png','jpeg','jpg','gif'];
    }

    public function load($data, $formName = null){
        
        if(parent::load($data, $formName)){

            
            
            $scope = $formName === null ? $this->formName() : $formName;
            if(isset($data[$scope]['loadedFile']) && $data[$scope]['loadedFile'] instanceof UploadedFile && !$data[$scope]['loadedFile']->hasError){
                $f = $data[$scope]['loadedFile'];
                $this->file_name = $f->basename;
                $this->file_type = $f->extension;
                $this->file = $this->raport_id."_".$f->basename."_".time().".".$f->extension;
                $this->file_binary = file_get_contents($f->tempName);
                $this->creator_id = Yii::$app->user->id;
            }
            

            return true;
        }

        return false;
    }

}