<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $login;
    public $password;
    public $rememberMe = true;

    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['login', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }



    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
        return array(
            'login'=>'Логин',
            'password'=>'Пароль',
            'rememberMe'=>'Запомнить меня'
            );
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect login or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            
            if($user->role->name == "admin"){
                return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
            }

            return false;
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[email]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByEmail($this->email);
        }

        return $this->_user;
    }

    protected function isAdmin(){

    }
}
