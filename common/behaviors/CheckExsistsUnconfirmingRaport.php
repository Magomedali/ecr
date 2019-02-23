<?php

namespace common\behaviors;

use Yii;
use yii\base\{Behavior,Controller};
use common\models\User;
use common\modules\CheckExistsUnconfirmingRaport;


class CheckExsistsUnconfirmingRaport extends Behavior{

	protected $user;


    public $actions = [];

    public $methods = ['GET'];

    public $redirect = ['site/index'];

    /**
    * Callable
    */
    public $exceptCondition = null;
    
    /**
    * Callable
    */
    public $errorCallback;


	/**
     * Initializes the [[rules]] array by instantiating rule objects from configurations.
     */
    public function init()
    {
        parent::init();
        if ($this->user !== false) {
            $this->user = Yii::$app->user->identity;
        }
    }


	public function beforeAction($event){

		$action_id = $event->action->id;

		if(!in_array($action_id, $this->actions)) return true;

        $verb = Yii::$app->getRequest()->getMethod();
        $allowed = array_map('strtoupper', $this->methods);
        if (!in_array($verb, $allowed)) {
            return true;
        }

        if($this->exceptCondition && is_callable($this->exceptCondition) && call_user_func($this->exceptCondition)){
            return true;
        }

		$checker = new CheckExistsUnconfirmingRaport($this->user);
        if(!$checker->isClosed()){

            if ($this->errorCallback !== null) {
                call_user_func($this->errorCallback, $this->user, $event->action);
            }else{
                $this->redirect();
            }

            return false;
        }

        return true;
	}


    protected function redirect()
    {
        if($this->redirect){
            return Yii::$app->getResponse()->redirect($this->redirect);
        }

        return Yii::$app->getResponse()->redirect(['site/index']);
    }

	/**
     * {@inheritdoc}
     */
    public function attach($owner)
    {
        $this->owner = $owner;
        $owner->on(Controller::EVENT_BEFORE_ACTION, [$this, 'beforeAction']);
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {
        if ($this->owner) {
            $this->owner->off(Controller::EVENT_BEFORE_ACTION, [$this, 'beforeAction']);
            $this->owner = null;
        }
    }

}