<?php

namespace common\base;

use Yii;
use yii\web\Controller as wController;

class Controller extends wController{



	/**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
       	
    	$user = Yii::$app->user->identity;

        $this->on(self::EVENT_BEFORE_ACTION,function() use($user){
        	if(isset($user->guid) && $user->guid && !boolval($user->is_master)){
        		\common\modules\notes\NoteInit::init($user);
        	}
        });

       	return parent::beforeAction($action);
    }
}