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
use common\models\NomenclatureOfTypeOfWorks;
use common\base\ActiveRecordVersionable;

class TypeOfWork extends ActiveRecordVersionable 
{
    
    public $nomenclatures_guid = [];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%typeofwork}}';
    }



    public static function versionableAttributes(){
        return [
            'guid',
            'name',
            'is_regulatory',
            'isDeleted',
        ];
    }

    

    public function rules(){
        return [
            // name, email, subject and body are required
            [['guid','name'], 'required'],
            //['guid','unique','targetClass' => '\common\models\TypeOfWork', 'message' => 'Запись с таким guid уже существует!'],
            [['guid'],'string','max'=>36],
            [['name'],'string','max'=>128],
            [['name'], 'filter','filter'=>function($v){return trim(strip_tags($v));}],
            ['is_regulatory','boolean'],
            ['is_regulatory','default','value'=>0],
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
            if(isset($data[$scope]['nomenclatures_guid']) && is_array($data[$scope]['nomenclatures_guid'])){
                $this->nomenclatures_guid = $data[$scope]['nomenclatures_guid'];
            }elseif(isset($data[$scope]['nomenclatures_guid']) && is_string($data[$scope]['nomenclatures_guid']) && $data[$scope]['nomenclatures_guid']){
                $this->nomenclatures_guid[] = $data[$scope]['nomenclatures_guid'];
            }

            return true;
        }

        return false;
    }



    public function saveRelationWithNomenclatures(){
        //Связываем тип работы с номенклатурами
        
        if($this->nomenclatures_guid && $this->guid){
            $this->nomenclatures_guid = array_unique($this->nomenclatures_guid);
            $inserts = [];
            foreach ($this->nomenclatures_guid as $value) {
                    $inserts[]=['typeofwork_guid'=>$this->guid,'nomenclature_guid'=>$value];
            }
            if(!$inserts) return false;
            
            $transaction = Yii::$app->db->beginTransaction();
            try {
                Yii::$app->db->createCommand()->delete(NomenclatureOfTypeOfWorks::tableName(),['typeofwork_guid'=>$this->guid])->execute();
                
                Yii::$app->db->createCommand()->batchInsert(NomenclatureOfTypeOfWorks::tableName(),['typeofwork_guid','nomenclature_guid'],$inserts)->execute();

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            } catch (\Throwable $e) {
                $transaction->rollBack();
                throw $e;
            }
            
        }elseif($this->guid){
            //Если объектов нет удаляем из базы, если они есть
            Yii::$app->db->createCommand()->delete(NomenclatureOfTypeOfWorks::tableName(),['typeofwork_guid'=>$this->guid])->execute();
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