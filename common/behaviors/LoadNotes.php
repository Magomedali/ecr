<?php

namespace common\behaviors;

use Yii;
use yii\base\{Behavior,Controller};
use common\models\User;
use common\modules\CheckCloseShift;


class LoadNotes extends Behavior{

	protected $user;

    public $methods = ['GET'];

	public $actions = [];



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
        
		$user = $this->user;
        if(isset($user->guid) && $user->guid && !boolval($user->is_master)){
        	\common\modules\notes\NoteInit::init($user);
        }

        Yii::$app->params['notes'] = \common\modules\notes\NoteCollections::getNotes();
        Yii::$app->params['notes_count'] = \common\modules\notes\NoteCollections::getCount();

        return true;
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