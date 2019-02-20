<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\Query;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use frontend\models\ExpensesManager;
use frontend\models\PaymentsExpenses;


use common\models\Raport;
use common\models\RaportMaterial;
use common\models\Brigade;
use common\models\Technic;
use common\models\RemnantsPackage;
use common\models\RemnantsItem;
use common\models\Nomenclature;
use common\base\ActiveRecordVersionable;
use common\models\NomenclatureOfTypeOfWorks;

use soapclient\methods\Unloadremnant;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;

    public $password;

    protected $brigade;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }



    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','guid'], 'required'],
            [['login','name','password'], 'filter', 'filter' => 'trim'],
            [['brigade_guid','technic_guid'],'default','value'=>null],
            [['ktu'],'number'],
            [['ktu'],'default','value'=>0],
            [['login','password'],'default','value'=>null],
            ['password','string','min'=>6],
            ['is_master','boolean'],
            [['is_master'],'default','value'=>false],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]]
        ];
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
        return array(
            'id'=>'Id',
            'guid'=>'Идентификатор в 1С',
            'name'=>'Ф.И.О',
            'login'=>'Логин',
            'ktu'=>'КТУ'
        );
    }

    public function load($data, $formName = null){
        
        if(parent::load($data, $formName)){

            if($this->login){
                //Обязателен пароль, если логин есть
                if(!$this->password){
                    $this->addError("password",'Password is required for login');
                    return false;
                }

                //Проверяем, есть ли другой пользователь с таким логином
                $user = self::find()->where("guid != '{$this->guid}'")->andWhere(['login'=>$this->login])->one();
                if(isset($user->id)){
                    $this->addError("login",'login is busy');
                    return false;
                }

                $this->setPassword($this->password);
                $this->generateAuthKey();
            }

            //Проверяем есть ли гуид бригады в базе
            if($this->brigade_guid){
                $br = Brigade::findOne(['guid'=>$this->brigade_guid]);
                if(!isset($br->id)){
                    $this->addError('brigade_guid',"'".$this->brigade_guid."' not exists on the site");
                    return false;
                }
            }

            //Проверяем есть ли гуид техники в базе
            if($this->technic_guid){
                $m = Technic::findOne(['guid'=>$this->technic_guid]);
                if(!isset($m->id)){
                    $this->addError('technic_guid',"'".$this->technic_guid."' not exists on the site");
                    return false;
                }
            }

            $scope = $formName === null ? $this->formName() : $formName;

            $model = self::find()->where(['guid'=>$this->guid])->one();
            if ($model && isset($model->id)) {
                $this->id = $model->id;

                //Если статус не был передан, то сохраняем предыдущий статус
                if(!isset($data[$scope]['status'])){
                    $this->status = $model->status;
                }

                $this->setOldAttributes($model->attributes);           
            }

            return true;
        }

        return false;
    }


    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        //Удаляем технику у других физ лиц, если она закреплена за другими пользователями
        if(($insert && $this->technic_guid) || (is_array($changedAttributes) && count($changedAttributes) 
            && isset($changedAttributes['technic_guid']) && $this->technic_guid)){
            Yii::$app->db->createCommand()->update(self::tableName(),['technic_guid'=>null],"technic_guid = '{$this->technic_guid}' AND id != {$this->id}")->execute();
        }
    }


    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by login
     *
     * @param string $login
     * @return static|null
     */
    public static function findByLogin($login)
    {
        return static::findOne(['login' => $login, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by guid
     *
     * @param string $guid
     * @return static|null
     */
    public static function findByGuid($guid)
    {
        return static::findOne(['guid' => $guid, 'status' => self::STATUS_ACTIVE]);
    }


    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {   
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }


    public function hasRole($role){
        if(!$role) return false;
        if(!$this->id) return false;

        return Yii::$app->authManager->checkAccess($this->id,$role);
    }


    public function hasRoles($roles = []){
        if(!count($roles)) return false;
        if(!$this->id) return false;

        $rolesUser = Yii::$app->authManager->getRolesByUser($this->id);

        if(!count($rolesUser)) return false;

        foreach ($roles as $r) {
            if(array_key_exists($r, $rolesUser)){
                return true;
            }
        }

        return false;
    }


    public function getBrigade(){
        if(!$this->brigade_guid) return null;

        if(!$this->brigade){
            $this->brigade = Brigade::findOne(["guid"=>$this->brigade_guid]);
        }

        return $this->brigade;
        
    }


    public function getBrigadeConsist(){

        if(!$this->brigade_guid || !$this->id) return [];

        $result = (new Query())->select(['u.guid as user_guid','u.name as user_name','u.ktu as user_ktu','u.technic_guid','t.name as technic_name'])
                    ->from(['u'=>self::tableName()])
                    ->leftJoin(['t'=>Technic::tableName()]," t.guid = u.technic_guid")
                    ->where(['brigade_guid'=>$this->brigade_guid])
                    ->orderBy(['u.ktu'=>SORT_DESC])
                    ->all();

        return $result;
    }





    public function groupRemnantItems($items){
        if(!is_array($items) || !count($items)) return false;

        //Группируем по номенклатуре
        $gItems = [];
        foreach ($items as $key => $item) {

            if(!isset($item['nomenclature_guid']) || !isset($item['count']))
                continue;

            if(!isset($gItems[$item['nomenclature_guid']])){
                $gItems[$item['nomenclature_guid']] = [];
            }

            $gItems[$item['nomenclature_guid']]['nomenclature_guid'] = $item['nomenclature_guid'];
            //задаем изначальное значение 0, если его не было
            $gItems[$item['nomenclature_guid']]['count'] = isset($gItems[$item['nomenclature_guid']]['count']) ? $gItems[$item['nomenclature_guid']]['count'] : 0;

            $gItems[$item['nomenclature_guid']]['count'] += $item['count'];
        }

        $remnants = [];
        foreach ($gItems as $key => $item) {
            $i = [];
            $i['nomenclature_guid'] = $item['nomenclature_guid'];
            $i['count'] = $item['count'];
            array_push($remnants, $i);
        }
        return $remnants;
    }



    public function remnantItemsIsEqual($gItems){
        if(!is_array($gItems) || !count($gItems)) return false;

        $isEqual = true;

        //Сортируем по возрастанию количества
        usort($gItems,function($a,$b){
            if($a['count'] > $b['count'])
                return 1;
            else if($a['count'] < $b['count']){
                return -1;
            }else{
                return 0;
            }
        });

        $actiuals =  (new Query())->select(['r.nomenclature_guid','r.count'])
                    ->from(['r'=>RemnantsItem::tableName()])
                    ->innerJoin(['rp'=>RemnantsPackage::tableName()]," r.package_id = rp.id")
                    ->where(['rp.user_guid'=>$this->guid,'rp.isActual'=>1])
                    ->orderBy(['count'=>SORT_ASC])
                    ->all();



        

        if(count($actiuals) != count($gItems)) return false;

        $length = count($gItems);

        

        for ($i=0; $i < $length; $i++) { 
            
            if(isset($actiuals[$i]['nomenclature_guid']) && isset($actiuals[$i]['count']) && isset($gItems[$i]['nomenclature_guid']) && isset($gItems[$i]['count'])){

                if($actiuals[$i]['nomenclature_guid'] != $gItems[$i]['nomenclature_guid'] || $actiuals[$i]['count'] != $gItems[$i]['count']){
                    
                    $isEqual = false;
                    break;
                }

            }else{
                $isEqual = false;
                break;
            }
            
        }

        return $isEqual;
    }

    





    public function disableActualRemnantsPackage(){
        if(!$this->id || !$this->guid) return false;
        return Yii::$app->db->createCommand()->update(RemnantsPackage::tableName(),['isActual'=>0],"`isActual`=1 AND `user_guid`='{$this->guid}'")
        ->execute();
    }




    public function unloadRemnantsFrom1C(){
        if(!$this->guid || !$this->id) return false;
        
        \common\modules\RemnantsDispatcher::loadFor($this);      
    }






    public function saveRemnants($items,$needToGroup = true){
        if(!is_array($items) || !count($items)) return false;

        $items = $needToGroup ? $this->groupRemnantItems($items) : $items;


        if($this->remnantItemsIsEqual($items)) return true;

        $data = [];
        $rm = new RemnantsPackage();
        $data['user_guid'] = $this->guid;
        $data['items'] = $items;
            
        if($rm->load(['RemnantsPackage'=>$data]) && $rm->save()){
            return $rm->saveRelationEntities();
        }
    }







    public function getRemnants($indexGuid = true){
        if(!$this->guid || !$this->id || !$this->brigade_guid) return [];

        $result = (new Query())->select('rp.user_guid, r.nomenclature_guid, r.count as was,  (null) as spent, r.count as rest, n.name as nomenclature_name, CASE when  `rtn`.`typeofwork_guid` > 0 THEN 1 ELSE 0 END as `assigned`')
                    ->from(['rp'=>RemnantsPackage::tableName()])
                    ->innerJoin(['r'=>RemnantsItem::tableName()]," r.package_id = rp.id")
                    ->innerJoin(['n'=>Nomenclature::tableName()]," r.nomenclature_guid = n.guid")
                    ->leftJoin(['rtn'=>NomenclatureOfTypeOfWorks::tableName()]," r.nomenclature_guid = rtn.nomenclature_guid")
                    ->where(['rp.user_guid'=>$this->guid,'rp.isActual'=>1])
                    ->groupBy(['r.nomenclature_guid'])
                    ->all();


        if(!$indexGuid) return $result;

        $remnants = [];
        foreach ($result as $key => $item) {
            $remnants[$item['nomenclature_guid']]['nomenclature_guid'] = $item['nomenclature_guid'];
            $remnants[$item['nomenclature_guid']]['nomenclature_name'] = $item['nomenclature_name'];
            $remnants[$item['nomenclature_guid']]['user_guid'] = $item['user_guid'];
            $remnants[$item['nomenclature_guid']]['was'] = $item['was'];
            $remnants[$item['nomenclature_guid']]['spent'] = $item['spent'];
            $remnants[$item['nomenclature_guid']]['rest'] = $item['rest'];
            $remnants[$item['nomenclature_guid']]['assigned'] = $item['assigned'];
        }

        return $remnants;
    }








    /**
    * @param $calcPrev - параметр указывающий нужно ли списывать материалы не принятых рапортов 
    **/
    public function getActualBrigadeRemnants($calcPrev = true,$toUpdate = true){
        if(!$this->guid || !$this->id || !$this->brigade_guid) return false;

        if($toUpdate){
            //Загрузим сначала из 1С;
            $this->unloadRemnantsFrom1C();
        }
        
        $remnants = $this->getRemnants();

        if(!$calcPrev) return $remnants;

        $prev = (new Query())->select(['r.id as raport_id','status','rm.nomenclature_guid','rm.spent'])->from(['r'=>Raport::tableName()])
                ->innerJoin(['rm'=>RaportMaterial::tableName()], "rm.raport_id = r.id")
                ->where(['brigade_guid'=>$this->brigade_guid])
                ->andWhere(['user_guid'=>$this->guid])
                ->andFilterWhere(["in",'status',Raport::getUnconfirmedStatuses()])
                ->all();

        

        $prevMaterial = [];
        foreach ($prev as $key => $item) {
            $prevMaterial[$item['raport_id']][$key]['nomenclature_guid'] = $item['nomenclature_guid'];
            $prevMaterial[$item['raport_id']][$key]['spent'] = $item['spent'];
        }

        //Списание материалов из неподтвержденных рапортов
        foreach ($prevMaterial as $key => $materials) {
            foreach ($materials as $key2 => $item) {
                if(array_key_exists($item['nomenclature_guid'], $remnants)){
                    $was = $remnants[$item['nomenclature_guid']]['was'];
                    $was -=$item['spent'];
                    $remnants[$item['nomenclature_guid']]['was'] = $was;
                    $remnants[$item['nomenclature_guid']]['rest'] = $was;
                }
            }
        }



        return $remnants;
    }


}
