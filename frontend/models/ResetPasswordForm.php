<?php
namespace frontend\models;

use Yii;
use yii\base\InvalidParamException;
use yii\base\Model;
use common\models\User;
use soapclient\methods\Useraccountload;
use common\models\Request;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{
    public $old_password;
    public $password;
    public $confirm_password;

    /**
     * @var \common\models\User
     */
    private $_user;


   

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password','confirm_password','old_password'], 'required','message'=>'Обязательное поле'],
            [['password','confirm_password','old_password'], 'string', 'min' => 6,'message'=>'Пароль должен иметь минимум 6 символов'],
            ['confirm_password', 'compare', 'compareAttribute'=>'password', 'message'=>"Пароли не совпадают!"],
            ['old_password','validateOldPassword']
        ];
    }


    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateOldPassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->old_password)) {
                $this->addError($attribute, 'Неверный пароль');
            }else{
                $this->_user = $user;
            }
        }
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
        return array(
            'old_password'=>'Текущий пароль',
            'password'=>'Новый пароль',
            'confirm_password'=>'Повторите новый пароль'
            );
    }


    /**
     * @return Current User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user =Yii::$app->user->identity;
        }

        return $this->_user;
    }

    /**
     * Resets password.
     *
     * @return boolean if password was reset.
     */
    public function resetPassword()
    {
        $user = $this->getUser();
        $user->setPassword($this->password);
        $user->removePasswordResetToken();

        if($user->save()){
            try {
            
                $method = new Useraccountload(['guid'=>$user->guid,'password'=>$this->password]);

                $request = new Request([
                        'request'=>get_class($method),
                        'params_in'=>json_encode($method->attributes),
                        'user_id'=>$user->id,
                        'actor_id'=>$user->id
                ]);

                if($request->save(1) && $request->send($method)){
                      
                }else{
                    
                }

            }catch(\Exception $e) {
                //throw $e;
            }

            return true;
        }
        
        
        return false;
    }
}
