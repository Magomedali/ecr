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

class Project extends ActiveRecordVersionable 
{
    
    protected static $rel_table_objects = "{{%rel_project_object}}";

    public $objects_guids = [];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%project}}';
    }


    /**
     * @inheritdoc
     */
    public static function tableNameRelObjects()
    {
        return self::$rel_table_objects;
    }



    public static function versionableAttributes(){
        return [
            'guid',
            'name',
            'isDeleted',
        ];
    }

    

    public function rules(){
        return [
            // name, email, subject and body are required
            [['guid','name'], 'required'],
            ['guid','unique','targetClass' => '\common\models\Project', 'message' => 'Запись с таким guid уже существует!'],
            [['guid'],'string','max'=>36],
            [['name'],'string','max'=>128],
            [['name'], 'filter','filter'=>function($v){return trim(strip_tags($v));}],
            
        ];
    }



    public function load($data, $formName = null){
        
        if(parent::load($data, $formName)){

            $model = self::find()->where(['guid'=>$this->guid])->one();
            if ($model && isset($model->id)) {
                $this->id = $model->id;
                $this->setOldAttributes($model->attributes);           
            }

            $scope = $formName === null ? $this->formName() : $formName;
            $this->objects_guids = isset($data[$scope]['objects_guids']) && is_array($data[$scope]['objects_guids']) ? $data[$scope]['objects_guids'] : [];

            return true;
        }

        return false;
    }
    
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        //Связываем объекты с проектом
        
        if($this->objects_guids && $this->guid){
            
            $inserts = [];
            foreach ($this->objects_guids as $value) {
                    $inserts[]=['project_guid'=>$this->guid,'object_guid'=>$value];
            }
            if(!$inserts) return false;
            
            $transaction = Yii::$app->db->beginTransaction();
            try {
                Yii::$app->db->createCommand()->delete(self::$rel_table_objects,['project_guid'=>$this->guid])->execute();
                
                Yii::$app->db->createCommand()->batchInsert(self::$rel_table_objects,['project_guid','object_guid'],$inserts)->execute();

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            } catch (\Throwable $e) {
                $transaction->rollBack();
                throw $e;
            }
            
        }elseif($this->guid){
            //Если оъъектов нет удаляем из базы, если они есть
            Yii::$app->db->createCommand()->delete(self::$rel_table_objects,['project_guid'=>$this->guid])->execute();
        }
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