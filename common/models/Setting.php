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

class Setting extends ActiveRecord 
{
    

    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%setting}}';
    }



    

    public function rules(){
        return [
            // name, email, subject and body are required
            [['shift_start_hours'], 'required'],
            [['name'],'string','max'=>255],
            ['is_actual','default','value'=>1],
            ['name','default','value'=>'default'],
            [['shift_start_hours'],'filter','filter'=>function($v){ return $v ? date("H:i:s",strtotime($v)) : date("H:i:s");}],
            [['name'], 'filter','filter'=>function($v){return trim(strip_tags($v));}],
        ];
    }

    public function load($data, $formName = null){
        
        if(parent::load($data, $formName)){

            $model = self::find()->one();
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
            'guid'=>'Идентификатор в 1С',
            'name'=>'Наименование'
        );
    }



    public static function getStartShiftTime(){
        $shift = self::find()->where(['is_actual'=>1])->one();

        if(!isset($shift->id)) return false;
        
        $cur_day = date("Y-m-d\T{$shift->shift_start_hours}");
        $now = date("Y-m-d\TH:i:s",time());
        
        if(strtotime($cur_day) < time()){
            return $cur_day;
        }else{
            return date("Y-m-d\TH:i:s",strtotime($cur_day)-86400);
        }
    }
}