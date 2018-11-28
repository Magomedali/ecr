<?php
namespace common\models;

use Yii;
use yii\db\{Expression,Query,Command};
use common\base\ActiveRecordVersionable;

use soapclient\methods\BaseMethod;

class Request extends ActiveRecordVersionable
{   

    const APP_INTEGRATE = 1;
    const GET_COST = 5;
    const PAY = 6;
    const GET_GROUPS = 7;

    const SESSION_START = 2;
    const SESSION_END = 3;
    const SESSION_PAY = 4;


    public static $type_requests = [
        self::APP_INTEGRATE=>"appsIntegrate",
        self::GET_COST => "getCost",
        self::GET_GROUPS => "getGroups",
        self::PAY => "pay",
        self::SESSION_START=>"SESSION_START",
        self::SESSION_END=>"SESSION_END",
        self::SESSION_PAY=>"SESSION_PAY"
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['request'], 'required'],

            ['created_at','default','value'=>date("Y-m-d\TH:i:s",time())],
            
            [['completed','result'],'default','value'=>0],
            [['raport_id','actor_id','user_id'],'default','value'=>null],
            [['params_out','completed_at','params_in'],'safe']
            
        ];
    }





    public static function tableName(){
        return '{{%requests}}';
    }


    public static function versionableAttributes(){
        return [
            'request',
            'created_at',
            'completed_at',
            'params_in',
            'params_out',
            'raport_id',
            'actor_id',
            'user_id',
            'result',
            'completed',
            'isDeleted',
        ];
    }


    public static function primaryKey(){
        return array('id');
    }




    public function attributeLabels(){
        return array(
            'id'=>'ID',
            'request'=>'Запрос',
            'type'=>'Тип запроса',
            'created_at'=>'Дата инициализации запроса',
            'completed_at' => 'Дата выполнения',
            'params_in'   => 'Входные параметры',
            'params_out' => 'Выходные параметры',
            'result'  => 'Результат',
            'completed' => 'Выполнен',
        );
    }

   
    



    public static function getNextTransactionId(){

        $sql = "SHOW TABLE STATUS WHERE name='requests'";

        $res = Yii::$app->db->createCommand($sql)
            ->queryOne();

       
        if(isset($res['Auto_increment'])){
            return (int)$res['Auto_increment'];
        }else{
            $sql = "SELECT `id` FROM ".self::tableName()." ORDER BY id DESC LIMIT 1";
            $last_id = Yii::$app->db->createCommand($sql)
            //->bindValue(":table",self::tableName())
            ->queryScalar();
            return $last_id+1;
        }

    }





    public function setParamIn($name,$value){

        $params_in = json_decode($this->params_in);
        if(is_object($params_in) && property_exists($params_in, $name)){

            $params_in->$name = $value;
            $params = [];
            foreach ($params_in as $key => $v) {
                $params[$key] = $v;
            }

            $this->params_in = json_encode($params);
            return $this->save();
            
        }
    }



    public function getParamIn($name,$array = false){

        $b = $array && 1;
        $params_in = json_decode($this->params_in,$b);
        
        if(is_object($params_in) && property_exists($params_in, $name)){
            return $params_in->$name;
        }elseif(is_array($params_in) && array_key_exists($name, $params_in)){
            return $params_in[$name];
        }
    }




    public function getParamOut($name,$array = false){
        
        $b = $array && 1;

        $params_out = json_decode($this->params_out,$b);
        if(is_object($params_out) && property_exists($params_out, $name)){
            return $params_out->$name;
        }elseif(is_array($params_out) && array_key_exists($name, $params_out)){
            return $params_out[$name];
        }
    }






    public function send(BaseMethod $method){

        try {
            
            if($method->validate()){
                $responce = Yii::$app->webservice1C->send($method);
                $responce = json_decode(json_encode($responce),1);
            }else{
                $responce = [
                    'success'=>false,
                    'error'=>'validateError',
                    'errorMessage'=>$method->getErrors()
                ];
            }
        

        } catch (\SoapFault $e) {
            
            $responce = [
                'success'=>false,
                'error'=>'SoapFault',
                'errorMessage'=>$e->getMessage()
            ];
        
        } catch(\Exception $e){
           
            $responce = [
                'success'=>false,
                'error'=>'SiteError',
                'errorMessage'=>$e->getMessage()
            ];
        
        }

        if(isset($responce['return']) &&  isset($responce['return']['success']) && (int)$responce['return']['success']){
            $this->result = 1;
            $this->completed = 1;
            $this->completed_at = date("Y-m-d\TH:i:s",time());
        }
        $this->params_out = json_encode($responce);
        
        $this->save();

        return boolval($responce['success']);
    }

}