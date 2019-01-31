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


class NomenclatureOfTypeOfWorks extends ActiveRecord 
{
    

    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%rel_typeofwork_nomenclature}}';
    }




    

    public function rules(){
        return [
            [['typeofwork_guid','nomenclature_guid'], 'required'],
            [['typeofwork_guid','nomenclature_guid'],'string','max'=>36],
        ];
    }



    public function load($data, $formName = null){
        
        if(parent::load($data, $formName)){

            $model = self::find()->where(['guid'=>$this->guid])->one();
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

}