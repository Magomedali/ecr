<?php
namespace backend\models;

use Yii;
use yii\base\InvalidParamException;
use yii\base\Model;

use soapclient\methods\Useraccountload;
use common\models\Request;
use common\models\User;

/**
 * Password reset form
 */
class ChangePassForm extends Model
{
    public $user_id;
    public $password;


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
            [['user_id','password'], 'required'],
            [['password'], 'string', 'min' => 6],
        ];
    }


    public function attributeLabels(){
        return array(
            'user_id'=>'Пользователь',
            'password'=>'Пароль'
        );
    }


    /**
     * @return Current User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findOne($this->user_id);
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
                        'actor_id'=>Yii::$app->user->id
                ]);

                if($request->save(1) && $request->send($method)){
                      
                }else{
                    
                }

            }catch(\Exception $e) {}

            return true;
        }

        return false;
    }
}
