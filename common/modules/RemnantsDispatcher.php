<?php

namespace common\modules;

use Yii;
use common\models\User;
use soapclient\methods\Unloadremnant;

class RemnantsDispatcher{

	protected static $loadedRemnants = [];


	public static function loadedAlready(User $user){
		
		return isset($user->guid) && $user->guid && array_key_exists($user->guid, self::$loadedRemnants);
	}

	public static function loadFor(User $user){
		if(!$user->guid || !$user->id) return false;

		if(self::loadedAlready($user)) return true;

		$method = new Unloadremnant(['guidmol'=>$user->guid]);

		$items = [];
        
        if($method->validate()){
            try {
                Yii::warning("Call unload remnant","unloadremnant");
                Yii::warning("Parameters","unloadremnant");
                Yii::warning(json_encode($method->parameters),"unloadremnant");
                $resp = Yii::$app->webservice1C->send($method);
                $resp = json_decode(json_encode($resp),1);

                $resp = isset($resp['return']) ? $resp['return'] : $resp;

                Yii::warning("Response","unloadremnant");
                Yii::warning(json_encode($resp),"unloadremnant");
                
                self::$loadedRemnants[$user->guid] = $resp;

                if(isset($resp['remnant'])){
                    $remnants = $resp['remnant'];
                    
                    \yii\helpers\ArrayHelper::isAssociative($remnants) ? $items[] = $remnants : $items = $remnants;
                    
                    return $user->saveRemnants($items);
                }elseif(isset($resp['success']) && (int)$resp['success']){
                    $user->disableActualRemnantsPackage();
                }

            }catch (\SoapFault $e) {
                Yii::error($e->getMessage(),"unloadremnant");
            }catch (\Exception $e) {
                Yii::error($e->getMessage(),"unloadremnant");
            }
        }
	}
}