<?php

namespace common\modules;

use yii\base\Model;
use common\models\User;


class TransferMaterials extends Model{



	/**
	* Associative multiple array
	* key: nomenclature_guid,count,series_guid
	*/
	protected $materials;

	protected $materialsError = [];

	public function rules(){
        return [
            // name, email, subject and body are required
            [['mol_guid','mol_guid_recipient'], 'required'],
            ['created_at','filter','filter'=>function($v){
                $date = $v ? date("Y-m-d\TH:i:s",strtotime($v)) : date("Y-m-d\TH:i:s",time());

                return $date;
            }],
            ['created_at','default','value'=>date("Y-m-d\TH:i:s",time())],
            [['comment'], 'filter','filter'=>function($v){return trim(strip_tags($v));}],
            [['comment',],'default','value'=>null],
            [['mol_guid','mol_guid_recipient'],'string','max'=>36],
            
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
                }else{
                    $this->user = $user;
                }
            }

            if($this->mol_guid_recipient){
                $m = User::findOne(['guid'=>$this->mol_guid_recipient,'is_master'=>1]);
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
            'name'=>'Наименование',
            'is_countable' => 'Количественный расчет',
            'hint_count' => 'Подсказка для количества',
            'hint_length'=> 'Подсказка для П.М./Шт'
        );
    }

}
?>