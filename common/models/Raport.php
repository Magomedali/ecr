<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

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

use soapclient\methods\RaportLoad;


class Raport extends ActiveRecordVersionable 
{
    
    protected  $materials = [];
    protected  $materialsErrors = [];

    protected  $works = [];
    protected  $worksErrors = [];

    protected  $consist = [];
    protected  $consistErrors = [];

    protected  $files = [];
    protected  $filesErrors = [];

    
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
            [['brigade_guid','object_guid','project_guid','master_guid','created_at'], 'required','message'=>'Обязательное поле'],
            
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
                    $this->addError('brigade_guid',"'".$this->brigade_guid."' not exists on the site");
                    return false;
                }
            }

            // //Проверяем есть ли гуид объекта в базе
            if($this->object_guid){
                $object = Objects::findOne(['guid'=>$this->object_guid]);
                if(!isset($object->id)){
                    $this->addError('object_guid',"'".$this->object_guid."' not exists on the site");
                    return false;
                }

                if($object->boundary_guid){
                    $this->boundary_guid = $object->boundary_guid;
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

            if(!count($this->materials)){
                $this->addError('materials',"doesn`t have materials");
                return false;
            }

            if(isset($data[$scope]['consist']) && is_array($data[$scope]['consist'])){
                $this->consist = $data[$scope]['consist'];
            }elseif(isset($data['RaportConsist']) && is_array($data['RaportConsist'])){
                $this->consist = $data['RaportConsist'];
            }else{
                $this->consist = [];
            }
            if(!count($this->consist)){
                $this->addError('consist',"doesn`t have consist");
                return false;
            }

            if(isset($data[$scope]['works']) && is_array($data[$scope]['works'])){
                $this->works = $data[$scope]['works'];
            }elseif(isset($data['RaportWork']) && is_array($data['RaportWork'])){
                $this->works = $data['RaportWork'];
            }else{
                $this->works = [];
            }

            if(!count($this->works)){
                $this->addError('works',"doesn`t have works");
                return false;
            }

            if(!isset($_FILES['files'])){
                if(isset($data[$scope]['files']) && is_array($data[$scope]['files'])){
                    $this->files = $data[$scope]['files'];
                }elseif(isset($data['RaportFile']) && is_array($data['RaportFile'])){
                    $this->files = $data['RaportFile'];
                }else{
                    $this->files = [];
                }
            }else{
                $this->files = UploadedFile::getInstancesByName('files');
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


    public function getFilesErrors(){
        return $this->filesErrors;
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


    public function getFiles(){
        if($this->id){
            return RaportFile::find()->where(['raport_id'=>$this->id])->asArray()->all();
        }else{
           return $this->files; 
        }
    }


    public function saveRelationEntities(){
        //Связываем материалы
        if($this->materials && $this->id){
            try {
                $transaction = Yii::$app->db->beginTransaction();

                $this->deleteMaterials();

                if($this->saveMaterials()){
                    $transaction->commit();
                }else{
                    $transaction->rollBack();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
            }
        }else{
            $this->deleteMaterials();
        }

        if($this->consist && $this->id){
            try {
                $transaction = Yii::$app->db->beginTransaction();

                $this->deleteConsist();

                if($this->saveConsist()){
                    $transaction->commit();
                }else{
                    $transaction->rollBack();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
            }
        }else{
            //Если оъъектов нет удаляем из базы, если они есть
            $this->deleteConsist();
        }

        if($this->works && $this->id){
            try {
                $transaction = Yii::$app->db->beginTransaction();

                $this->deleteWorks();

                if($this->saveWorks()){
                    $transaction->commit();
                }else{
                    $transaction->rollBack();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
            }
        }else{
            //Если оъъектов нет удаляем из базы, если они есть
            $this->deleteWorks();
        }

        if($this->files && $this->id){
            try {
                //$transaction = Yii::$app->db->beginTransaction();

                //$this->deleteFiles();

                if($this->saveFiles()){
                //    $transaction->commit();
                }else{
                 //   $transaction->rollBack();
                }
            } catch (\Exception $e) {
               // $transaction->rollBack();
            }
        }
    }








    
    public function saveMaterials($data = []){
        if(!$this->id) return false;

        $materials = count($data) ? $data : $this->materials;

        if(!is_array($materials)){
            return false;
        }
        
        $Type = "RaportMaterial";
        if(!isset($materials[$Type])){
            $materials[$Type] = $materials;
        }
        
        if(!array_key_exists(0, $materials[$Type])){
            $materials[$Type] =  [$materials[$Type]];
        }

        foreach ($materials[$Type] as $key => $mdata) {
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

        if(!is_array($consist)){
            return false;
        }

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











    public function saveFiles($data = []){
        if(!$this->id) return false;

        $files = count($data) ? $data : $this->files;
        if(!is_array($files)){
            return false;
        }

        $Type = "RaportFile";
        if(!isset($files[$Type])){
            $files[$Type] = $files;
        }
        
        if(!array_key_exists(0, $files[$Type])){
            $files[$Type] =  [$files[$Type]];
        }
        
        foreach ($files[$Type] as $key => $mdata) {
            $model = new RaportFile();

            if($mdata instanceof UploadedFile){
                $arData['loadedFile'] = $mdata;
            }else{
                $arData = is_object($mdata) ? json_decode(json_encode($mdata),1) : $mdata;
            }
            
            $arData['raport_id'] = $this->id;

            if(!$model->load(['RaportFile'=>$arData]) || !$model->save()){
                $this->filesErrors[] = json_encode($model->getErrors());
            }
        }

        return !count($this->filesErrors);
    }

    public function deleteFiles($data = []){
        if(!$this->id) return false;

        Yii::$app->db->createCommand()->delete(RaportFile::tableName(),['raport_id'=>$this->id])->execute();
    }






    public function sendToConfirmation(){
        if(!$this->id) return false;

        try {
            $method = new RaportLoad();
            $params = $this->getAttributes(null,[
                'id',
                'status',
                'guid',
                'isDeleted',
                'version_id',
                'number'
            ]);

            
            $params['works'] = (new Query)->select(['work_guid','line_guid','mechanized','length','count','squaremeter'])->from(RaportWork::tableName())->where(['raport_id'=>$this->id])->all();

            $params['consist'] = (new Query)->select(['user_guid','technic_guid'])->from(RaportConsist::tableName())->where(['raport_id'=>$this->id])->all();
            
            $params['materials'] = (new Query)->select(['nomenclature_guid','spent as count'])->from(RaportMaterial::tableName())->where(['raport_id'=>$this->id])->all();
            
            
            $user = Yii::$app->user->identity;
            
            $params['user_guid'] = $user->guid;

            //$files = (new Query)->select(['file_binary as file','file_type as type','file_name'])->from(RaportFile::tableName())->where(['raport_id'=>$this->id])->all();

            // $minFiles = [];
            // foreach ($files as $key => $f) {
            //      $minFiles[$key]['type'] = $f['type'];
            //      $minFiles[$key]['file_name'] = $f['file_name'];
            //  } 

            //$params['files'] = $minFiles;

            $request = new Request([
                'request'=>get_class($method),
                'params_in'=>json_encode($params),
                'raport_id'=>$this->id,
                'actor_id'=>$user->id
            ]);

            //$params['files'] = $files;

            $method->setParameters($params);
            if($request->save() && $request->send($method)){
                $responce = json_decode($request->params_out,1);
                if($request->result && isset($responce['guid']) && $responce['guid'] && isset($responce['number']) && $responce['number']){
                    $this->guid = $responce['guid'];
                    $this->number = $responce['number'];

                    if($this->status == RaportStatuses::CREATED){
                        $this->status = RaportStatuses::IN_CONFIRMING;
                    }
                    
                    return $this->save(1);
                }
                       
            }
        } catch (\Exception $e) {
            
        }
        

        return false;
    }




    public static function getUnconfirmedStatuses(){
        return [
            RaportStatuses::CREATED,
            RaportStatuses::IN_CONFIRMING
        ];
    }
}