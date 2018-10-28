<?php
namespace frontend\models;

use common\models\User;
use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;

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
            [['password','confirm_password','old_password'], 'required'],
            [['password','confirm_password','old_password'], 'string', 'min' => 6],
        ];
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
     * Resets password.
     *
     * @return boolean if password was reset.
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();

        return $user->save(false);
    }
}
