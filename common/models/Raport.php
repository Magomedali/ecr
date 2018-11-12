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
use common\models\Objects;
use common\models\Brigade;
use common\models\Project;

use common\models\RaportConsist;
use common\models\RaportMaterial;
use common\models\RaportWork;

use common\base\ActiveRecordVersionable;
use common\dictionaries\RaportStatuses;


class Raport extends ActiveRecordVersionable 
{
    
    protected  $materials = [];
    protected  $materialsErrors = [];

    protected  $works = [];
    protected  $worksErrors = [];

    protected  $consist = [];
    protected  $consistErrors = [];

    protected  $files = [];

    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%raport}}';
    }



    public static function versionableAttributes(){
        return [
            'guid',
            'number',
            'status',
            'created_at',
            'starttime',
            'endtime',
            'temperature_start',
            'temperature_end',
            'surface_temperature_start',
            'surface_temperature_end',
            'airhumidity_start',
            'airhumidity_end',
            'brigade_guid',
            'object_guid',
            'boundary_guid',
            'project_guid',
            'master_guid',
            'comment',

            'isDeleted',
        ];
    }

    


	public function rules(){
		return [
            // name, email, subject and body are required
            [['guid','brigade_guid','object_guid','boundary_guid','project_guid','master_guid','created_at'], 'required'],
            
            [['number','comment'], 'filter','filter'=>function($v){return trim(strip_tags($v));}],
            
            [['created_at'],'filter','filter'=>function($v){ return $v ? date("Y-m-d\TH:i:s",strtotime($v)) : date("Y-m-d\TH:i:s");}],

            [['starttime','endtime'],'filter','filter'=>function($v){ return $v ? date("H:i:s",strtotime($v)) : date("H:i:s");}],

            [['temperature_start','temperature_end','surface_temperature_start','surface_temperature_end','airhumidity_start','airhumidity_end'],'number'],

            [['guid','brigade_guid','object_guid','boundary_guid','project_guid','master_guid'],'string','max'=>32],
            
            ['number', 'string', 'max' => 255],
           
            ['status', 'default', 'value' => RaportStatuses::CREATED],
            ['status', 'in', 'range' => [
                RaportStatuses::CREATED, 
                RaportStatuses::IN_CONFIRMING,
                RaportStatuses::CONFIRMED,
                RaportStatuses::DELETED]
            ],
        ];
	}


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
        return array(
            'id'=>'Id',
            'guid'=>'Идентификатор 1С',
            'number'=>'Номер',
            'status'=>'Статус',
            'created_at'=>'Дата',
            'starttime'=>'Время начало работ',
            'endtime'=>'Время окончания работ',
            'temperature_start'=>"",
            'temperature_end'=>"",
            'surface_temperature_start'=>"",
            'surface_temperature_end'=>"",
            'airhumidity_start'=>"",
            'airhumidity_end'=>"",
            'brigade_guid'=>"Бригада",
            'object_guid'=>"Объект",
            'boundary_guid'=>"Округ",
            'project_guid'=>"Контракт",
            'master_guid'=>"Мастер",
            'comment'=>"Комментарии",
            'isDeleted'=>"Удалена",
        );
    }




    public function load($data, $formName = null){
        
        if(parent::load($data, $formName)){

            $code = RaportStatuses::getCode($this->status);
            $this->status = $code ? $code : null;
            
            //Проверяем есть ли гуид бригады в базе
            if($this->brigade_guid){
                $br = Brigade::findOne(['guid'=>$this->brigade_guid]);
                if(!isset($br->id)){
                    $this->addError('brigade_guid',"Бригада с таким guid отсутствует в базе");
                    return false;
                }
            }

            // //Проверяем есть ли гуид объекта в базе
            if($this->object_guid){
                $m = Objects::findOne(['guid'=>$this->object_guid]);
                if(!isset($m->id)){
                    $this->addError('object_guid',"Объект с таким guid отсутствует в базе");
                    return false;
                }
            }

            if($this->boundary_guid){
                $m = Boundary::findOne(['guid'=>$this->boundary_guid]);
                if(!isset($m->id)){
                    $this->addError('boundary_guid',"Граница с таким guid отсутствует в базе");
                    return false;
                }
            }

            if($this->project_guid){
                $m = Project::findOne(['guid'=>$this->project_guid]);
                if(!isset($m->id)){
                    $this->addError('project_guid',"Проект с таким guid отсутствует в базе");
                    return false;
                }
            }

            if($this->master_guid){
                $m = User::findOne(['guid'=>$this->master_guid,'is_master'=>1]);
                if(!isset($m->id)){
                    $this->addError('master_guid',"Мастер с таким guid отсутствует в базе");
                    return false;
                }
            }

            $model = self::find()->where(['guid'=>$this->guid])->one();
            if ($model && isset($model->id)) {
                $this->id = $model->id;
                $this->setOldAttributes($model->attributes);           
            }

            $scope = $formName === null ? $this->formName() : $formName;
            
            $this->materials = isset($data[$scope]['materials']) && is_array($data[$scope]['materials']) ? $data[$scope]['materials'] : [];

            $this->consist = isset($data[$scope]['consist']) && is_array($data[$scope]['consist']) ? $data[$scope]['consist'] : [];

            $this->works = isset($data[$scope]['works']) && is_array($data[$scope]['works']) ? $data[$scope]['works'] : [];

            $this->files = isset($data[$scope]['files']) && is_array($data[$scope]['files']) ? $data[$scope]['files'] : [];

            return true;
        }

        return false;
    }


    public function getMaterialsErrors(){
        return $this->materialsErrors;
    }

    public function getConsistErrors(){
        return $this->consistErrors;
    }

    public function getWorksErrors(){
        return $this->worksErrors;
    }


    public function afterSave($insert, $changedAttributes){
        parent::afterSave($insert, $changedAttributes);

        //Связываем материалы
        if($this->materials && $this->id){
            $this->saveMaterials();
        }else{
            $this->deleteMaterials();
        }

        if($this->consist && $this->id){
            $this->saveConsist();
        }else{
            //Если оъъектов нет удаляем из базы, если они есть
            $this->deleteConsist();
        }

        if($this->works && $this->id){
            $this->saveWorks();
        }else{
            //Если оъъектов нет удаляем из базы, если они есть
            $this->deleteWorks();
        }
    }


    
    public function saveMaterials($data = []){
        if(!$this->id) return false;

        $materials = count($data) ? $data : $this->materials;

        foreach ($materials as $key => $mdata) {
            $model = new RaportMaterial();

            $arData = is_object($mdata) ? json_decode(json_encode($mdata),1) : $mdata;
            $arData['raport_id'] = $this->id;

            if(!$model->load(['RaportMaterial'=>$arData]) || !$model->save()){
                $this->materialsErrors[$model->nomenclature_guid] = json_encode($model->getErrors());
            }
        }

        return !count($this->materialsErrors);
    }


    public function deleteMaterials($data = []){
        if(!$this->id) return false;

        Yii::$app->db->createCommand()->delete(RaportMaterial::tableName(),['raport_id'=>$this->id])->execute();
    }







    public function saveConsist($data = []){
        if(!$this->id) return false;

        $consist = count($data) ? $data : $this->consist;

        foreach ($consist as $key => $mdata) {
            $model = new RaportConsist();

            $arData = is_object($mdata) ? json_decode(json_encode($mdata),1) : $mdata;
            $arData['raport_id'] = $this->id;

            if(!$model->load(['RaportConsist'=>$arData]) || !$model->save()){
                $this->consistErrors[$model->user_guid] = json_encode($model->getErrors());
            }
        }

        return !count($this->consistErrors);
    }


    public function deleteConsist($data = []){
        if(!$this->id) return false;

        Yii::$app->db->createCommand()->delete(RaportConsist::tableName(),['raport_id'=>$this->id])->execute();
    }
    
    


    public function saveWorks($data = []){
        if(!$this->id) return false;

        $works = count($data) ? $data : $this->works;

        foreach ($works as $key => $mdata) {
            $model = new RaportWork();

            $arData = is_object($mdata) ? json_decode(json_encode($mdata),1) : $mdata;
            $arData['raport_id'] = $this->id;

            if(!$model->load(['RaportWork'=>$arData]) || !$model->save()){
                $this->worksErrors[$model->work_guid] = json_encode($model->getErrors());
            }
        }

        return !count($this->worksErrors);
    }


    public function deleteWorks($data = []){
        if(!$this->id) return false;

        Yii::$app->db->createCommand()->delete(RaportWork::tableName(),['raport_id'=>$this->id])->execute();
    }


}