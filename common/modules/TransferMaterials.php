<?php

namespace common\modules;

use yii\db\Query;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\{Request,Document};
use common\dictionaries\ExchangeStatuses;
use soapclient\methods\TransferOfMaterials;


class TransferMaterials extends Model{

    public $guid = null;

    public $mol_guid;
    
    public $mol_guid_recipient;
    
    public $date;
    
    public $comment;

    public $status;
    

	/**
	* Associative multiple array
	* key: nomenclature_guid,count,series_guid
	*/
	protected $materials;

	protected $materialsError = [];

    protected $request_id = null;


	public function rules(){
        return [
            // name, email, subject and body are required
            [['mol_guid','mol_guid_recipient'], 'required'],
            ['date','filter','filter'=>function($v){
                $date = $v ? date("Y-m-d\TH:i:s",strtotime($v)) : date("Y-m-d\TH:i:s",time());

                return $date;
            }],
            ['date','default','value'=>date("Y-m-d\TH:i:s",time())],
            [['comment'], 'filter','filter'=>function($v){return trim(strip_tags($v));}],
            [['comment','guid'],'default','value'=>null],
            [['mol_guid','mol_guid_recipient','guid'],'string','max'=>36],
            ['status','default','value'=>Document::STATUS_ACCEPTED]
        ];
    }



    public function load($data, $formName = null){
        
        if(parent::load($data, $formName)){

            //Проверяем есть ли гуид бригады в базе
            if($this->mol_guid){
                $user = User::findOne(['guid'=>$this->mol_guid]);
                if(!isset($user->id)){
                    $this->addError('mol_guid',"User '".$this->mol_guid."' not exists on the site");
                    return false;
                }
            }

            if($this->mol_guid_recipient){
                $m = User::findOne(['guid'=>$this->mol_guid_recipient,'is_master'=>0]);
                if(!isset($m->id)){
                    $this->addError('mol_guid_recipient',"Master ".$this->mol_guid_recipient." not exists on the site");
                    return false;
                }
            }

            $scope = $formName === null ? $this->formName() : $formName;
            
            if(isset($data[$scope]['materials']) && is_array($data[$scope]['materials'])){
                $this->materials = $data[$scope]['materials'];
            }elseif(isset($data['materials']) && is_array($data['materials'])){
                $this->materials = $data['materials'];
            }else{
                $this->materials = [];
            }

            if(!count($this->materials)){
                $this->addError('materials',"doesn`t have materials");
                return false;
            }else{
                if(!$this->checkAndFilterMaterials()) return false;
            }

            return true;
        }

        return false;
    }

    public function checkAndFilterMaterials(){
        if(!count($this->materials)){
            $this->addError('materials',"Не указаны материалы для передачи!");
            return false;
        }

        $this->materialsError = [];
        $filterMaterials = [];
        foreach ($this->materials as $key => $material) {
            if(!isset($material['nomenclature_guid']) || !$material['nomenclature_guid']
                 || !isset($material['series_guid']) || !$material['series_guid'] 
                 || !isset($material['send'])){
                array_push($this->materialsError, "Отсутствуют обязательные параметры!");
                continue;
            }

            if($material['send'] > 0){
                if(isset($material['count']) && $material['send'] > $material['count']){
                    array_push($this->materialsError, "Недостаточное количество материала");
                    continue;
                }

                $filterMaterials[$key]['nomenclature_guid'] = $material['nomenclature_guid'];
                $filterMaterials[$key]['series_guid'] = $material['series_guid'];
                $filterMaterials[$key]['count'] = $material['send'];
            }

        }

        if(!count($filterMaterials)){
            $this->addError('materials',"Не указаны материалы для передачи!");
            return false;
        }

        $this->materials = $filterMaterials;

        return count($this->materials);
    }

    
    public function getMaterialsError(){
        return $this->materialsError;
    }



    public function getMaterials(){
        return $this->materials;
    }



    public function getUnLoadedStructuredMaterials(){
        if(!is_array($this->materials) || !count($this->materials)) return [];

        if(ArrayHelper::isAssociative($this->materials)){
            $this->materials =  [$this->materials];
        }
        
        $structured = [];
        foreach ($this->materials as $key => $m) {
            $structured[$m['nomenclature_guid']][$m['series_guid']]['count'] = $m['count'];
        }

        return $structured;
    }


    public function setMaterials($materials){
        return $this->materials = $materials;
    }






    public function setRequestId($request_id){
        $this->request_id = $request_id;
    }





    public function getRequestId(){
        return $this->request_id;
    }




    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
        return array(
            'id'=>'Id',
            'guid'=>'Идентификатор в 1С',
            'date'=>'Дата создания документа',
            'comment'=>'Комментарий',
        );
    }



    public static function loadFromRequest($request){
        $req = (new Query())->select(['id','params_in'])
                ->from(Request::tableName())
                ->where(['id'=>$request])
                ->one();


        if(isset($req['params_in'])){
            $params = json_decode($req['params_in'],1);
            $model = new static($params);

            $model->setRequestId($request);

            return $model;
        }

        return new static();
    }


    public static function getActualTransfersFromUser($user_id){
        if(!$user_id) return [];
        $docs = (new Query())->select(['id','params_in'])
                ->from(Request::tableName())
                ->where([
                    'request'=>get_class(new TransferOfMaterials),
                    'user_id'=>$user_id,
                    'result'=>0,
                    'completed'=>0])
                ->all();

        return $docs;
    }

}
?>