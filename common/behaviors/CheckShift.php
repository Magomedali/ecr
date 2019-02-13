<?php

namespace common\behaviors;

use Yii;
use yii\base\{Behavior,Controller};
use common\models\User;
use common\modules\CheckCloseShift;


class CheckShift extends Behavior{

	protected $user;

	public $actions = [];


	public $redirect = ['site/index'];


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

		$checkerShift = new CheckCloseShift($this->user);
        if(!$checkerShift->isClosed()){
            return $this->owner->redirect($this->redirect);
        }
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




    /**
     * Returns an action ID by converting [[Action::$uniqueId]] into an ID relative to the module.
     * @param Action $action
     * @return string
     * @since 2.0.7
     */
    protected function getActionId($action)
    {
        if ($this->owner instanceof Module) {
            $mid = $this->owner->getUniqueId();
            $id = $action->getUniqueId();
            if ($mid !== '' && strpos($id, $mid) === 0) {
                $id = substr($id, strlen($mid) + 1);
            }
        } else {
            $id = $action->id;
        }

        return $id;
    }
}