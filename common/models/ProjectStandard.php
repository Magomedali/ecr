<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;

use common\models\Project;
use common\models\TypeOfWork;

class ProjectStandard extends ActiveRecord 
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%project_standard}}';
    }

    

    public function rules(){
        return [
            // name, email, subject and body are required
            [['project_guid','typeofwork_guid','standard'], 'required','message'=>'message'],
            [['project_guid','typeofwork_guid'],'string','max'=>36],
            [['standard'],'number'],
            [['standard'], 'default','value'=>0],
        ];
    }



    public function load($data, $formName = null){
        
        if(parent::load($data, $formName)){


            if($this->project_guid){
                $m = Project::findOne(['guid'=>$this->project_guid]);
                if(!isset($m->id)){
                    $this->addError('project_guid',"Project ".$this->project_guid." not exists on the site");
                    return false;
                }
            }

            //Проверяем есть ли гуид номенклатуры в базе
            if($this->typeofwork_guid){
                $m = TypeOfWork::findOne(['guid'=>$this->typeofwork_guid]);
                if(!isset($m->id)){
                    $this->addError('typeofwork_guid',"'".$this->typeofwork_guid."' not exists on the site");
                    return false;
                }
            }

            $model = self::find()->where(['project_guid'=>$this->project_guid,'typeofwork_guid'=>$this->typeofwork_guid])->one();
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
            'typeofwork_guid'=>'Вид работы',
            'project_guid'=>'Проект',
            'standard'=>'Норматив'
        );
    }

}