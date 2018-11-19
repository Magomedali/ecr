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
use common\models\Nomenclature;
use common\models\TypeOfWork;
use common\models\Line;
use common\models\Remnant;

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
            [['brigade_guid','object_guid','boundary_guid','project_guid','master_guid','created_at'], 'required','message'=>'Обязательное поле'],
            
            [['number','comment'], 'filter','filter'=>function($v){return trim(strip_tags($v));}],
            
            [['created_at'],'filter','filter'=>function($v){ return $v ? date("Y-m-d\TH:i:s",strtotime($v)) : date("Y-m-d\TH:i:s");}],

            [['starttime','endtime'],'filter','filter'=>function($v){ return $v ? date("H:i:s",strtotime($v)) : date("H:i:s");}],

            [['temperature_start','temperature_end','surface_temperature_start','surface_temperature_end','airhumidity_start','airhumidity_end'],'number'],

            [['guid','brigade_guid','object_guid','boundary_guid','project_guid','master_guid'],'string','max'=>36],
            
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
            'temperature_start'=>"Темп. воздх. до",
            'temperature_end'=>"Темп. воздх. после",
            'surface_temperature_start'=>"Темп. поверх. до",
            'surface_temperature_end'=>"Темп. поверх. после",
            'airhumidity_start'=>"Влажность воздх. до",
            'airhumidity_end'=>"Влажность воздх. после",
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

            if(!isset($this->id) && $this->guid){
                $model = self::find()->where(['guid'=>$this->guid])->one();
                if ($model && isset($model->id)) {
                    $this->id = $model->id;
                    $this->setOldAttributes($model->attributes);           
                } 
            }
            

            $scope = $formName === null ? $this->formName() : $formName;
            
            if(isset($data[$scope]['materials']) && is_array($data[$scope]['materials'])){
                $this->materials = $data[$scope]['materials'];
            }elseif(isset($data['RaportMaterial']) && is_array($data['RaportMaterial'])){
                $this->materials = $data['RaportMaterial'];
            }else{
                $this->materials = [];
            }


            if(isset($data[$scope]['consist']) && is_array($data[$scope]['consist'])){
                $this->consist = $data[$scope]['consist'];
            }elseif(isset($data['RaportConsist']) && is_array($data['RaportConsist'])){
                $this->consist = $data['RaportConsist'];
            }else{
                $this->consist = [];
            }

            if(isset($data[$scope]['works']) && is_array($data[$scope]['works'])){
                $this->works = $data[$scope]['works'];
            }elseif(isset($data['RaportWork']) && is_array($data['RaportWork'])){
                $this->works = $data['RaportWork'];
            }else{
                $this->works = [];
            }


            if(isset($data[$scope]['files']) && is_array($data[$scope]['files'])){
                $this->files = $data[$scope]['files'];
            }elseif(isset($data['RaportFile']) && is_array($data['RaportFile'])){
                $this->files = $data['RaportFile'];
            }else{
                $this->files = [];
            }


            return true;
        }

        return false;
    }

    public function getObject(){
        return $this->hasOne(Objects::className(),["guid"=>'object_guid']);
    }

    public function getProject(){
        return $this->hasOne(Project::className(),["guid"=>'project_guid']);
    }


    public function getMaster(){
        return $this->hasOne(User::className(),["guid"=>'master_guid']);
    }


    public function getStatusTitle(){
        $title = RaportStatuses::getLabels($this->status);

        return !is_array($title) ? $title : null;
    }

    public function getIsCanUpdate(){
        return $this->status <= RaportStatuses::IN_CONFIRMING;
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


    public function getMaterials(){
        if($this->id){
            return (new Query)->select(['rm.*','n.name as nomenclature_name'])->from(['rm'=>RaportMaterial::tableName()])
                                ->innerJoin(['n'=>Nomenclature::tableName()]," n.guid = rm.nomenclature_guid")
                                ->where(['raport_id'=>$this->id])
                                ->all();
        }else{
           return $this->materials; 
        }
    }

    public function getConsist(){
        if($this->id){
            return (new Query)->select(['u.name as user_name','u.ktu as user_ktu','u.guid as user_guid','t.guid as technic_guid','t.name as technic_name'])->from(['rc'=>RaportConsist::tableName()])
                                ->innerJoin(['u'=>User::tableName()]," u.guid = rc.user_guid")
                                ->innerJoin(['t'=>Technic::tableName()]," t.guid = rc.technic_guid")
                                ->where(['raport_id'=>$this->id])
                                ->all();
        }else{
           return $this->consist; 
        }
    }

    public function getWorks(){
        if($this->id){
            return (new Query)->select(['rw.line_guid','l.name as line_name','rw.work_guid','tw.name as work_name','rw.mechanized','rw.length','rw.count','rw.squaremeter'])->from(['rw'=>RaportWork::tableName()])
                                ->innerJoin(['tw'=>TypeOfWork::tableName()]," tw.guid = rw.work_guid")
                                ->innerJoin(['l'=>Line::tableName()]," l.guid = rw.line_guid")
                                ->where(['raport_id'=>$this->id])
                                ->all();
        }else{
           return $this->works; 
        }
    }


    public function afterSave($insert, $changedAttributes){
        parent::afterSave($insert, $changedAttributes);

        //Связываем материалы
        if($this->materials && $this->id){
            //Начало транзакции
            //удаляем
            $this->deleteMaterials();
            //обновляем
            $this->saveMaterials();
            //Конец транзакции
        }else{
            $this->deleteMaterials();
        }

        if($this->consist && $this->id){
            //Начало транзакции
            //удаляем
            $this->deleteConsist();
            //обновляем
            $this->saveConsist();
            //Конец транзакции
        }else{
            //Если оъъектов нет удаляем из базы, если они есть
            $this->deleteConsist();
        }

        if($this->works && $this->id){
            //Начало транзакции
            //удаляем
            $this->deleteWorks();
            //обновляем
            $this->saveWorks();
            //Конец транзакции
        }else{
            //Если оъъектов нет удаляем из базы, если они есть
            $this->deleteWorks();
        }
    }


    
    public function saveMaterials($data = []){
        if(!$this->id) return false;

        $materials = count($data) ? $data : $this->materials;

        if(!is_array($materials)){
            return false;
        }
        
        Yii::error(json_encode($materials),"api");
        $Type = "RaportMaterial";
        if(!isset($materials[$Type])){
            $materials[$Type] = $materials;
        }
        
        if(!array_key_exists(0, $materials[$Type])){
            $materials[$Type] =  [$materials[$Type]];
        }

        foreach ($materials[$Type] as $key => $mdata) {
            $model = new RaportMaterial();
            Yii::error(json_encode($mdata),"api");
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

        if(!is_array($consist)){
            return false;
        }
        // print_r($materials);
        Yii::error(json_encode($consist),"api");
        $Type = "RaportConsist";
        if(!isset($consist[$Type])){
            $consist[$Type] = $consist;
        }
        
        if(!array_key_exists(0, $consist[$Type])){
            $consist[$Type] =  [$consist[$Type]];
        }
        foreach ($consist[$Type] as $key => $mdata) {
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
        if(!is_array($works)){
            return false;
        }
        
        $Type = "RaportWork";
        if(!isset($works[$Type])){
            $works[$Type] = $works;
        }
        
        if(!array_key_exists(0, $works[$Type])){
            $works[$Type] =  [$works[$Type]];
        }
        foreach ($works[$Type] as $key => $mdata) {
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