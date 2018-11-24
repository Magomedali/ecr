<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\Query;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use frontend\models\ExpensesManager;
use frontend\models\PaymentsExpenses;

use common\models\Brigade;
use common\models\Technic;
use common\models\RemnantsPackage;
use common\models\RemnantsItem;
use common\models\Nomenclature;

use common\base\ActiveRecordVersionable;

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
    const STATUS_ACTIVE = 10;

    public $password;

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
            ['is_master','boolean'],
            [['is_master'],'default','value'=>false],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]]
        ];
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

            $model = self::find()->where(['guid'=>$this->guid])->one();
            if ($model && isset($model->id)) {
                $this->id = $model->id;
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








    public function getBrigadeConsist(){

        if(!$this->brigade_guid || !$this->id) return false;

        $result = (new Query())->select(['u.guid as user_guid','u.name as user_name','u.ktu as user_ktu','u.technic_guid','t.name as technic_name'])
                    ->from(['u'=>self::tableName()])
                    ->leftJoin(['t'=>Technic::tableName()]," t.guid = u.technic_guid")
                    ->where(['brigade_guid'=>$this->brigade_guid])
                    ->all();

        return $result;
    }


    public function getActualBrigadeRemnants(){
        if(!$this->brigade_guid || !$this->id) return false;

        $result = (new Query())->select('rp.brigade_guid, r.nomenclature_guid, r.count as was, r.count as rest, (null) as spent, n.name as nomenclature_name')
                    ->from(['rp'=>RemnantsPackage::tableName()])
                    ->innerJoin(['r'=>RemnantsItem::tableName()]," r.package_id = rp.id")
                    ->innerJoin(['n'=>Nomenclature::tableName()]," r.nomenclature_guid = n.guid")
                    ->where(['rp.brigade_guid'=>$this->brigade_guid,'rp.isActual'=>1])
                    ->all();

        return $result;
    }


}
