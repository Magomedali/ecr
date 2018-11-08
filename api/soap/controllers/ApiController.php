<?php
namespace api\soap\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use api\soap\models\Brigade;


/**
 * Api controller
 */
class ApiController extends Controller
{   


    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'index' => [
                'class'=>'mongosoft\soapserver\Action',
                
            ]
        ];
    }



    /**
     * unload Brigade
     * @param api\soap\models\Brigade[] $brigades
     * @param int $id
     * @return api\soap\models\Brigade $v
     * @soap
     */
    public function unloadbrigade($brigades)
    {   
        $v = $brigades[$id];
        return $v;
    }


    /**
     * Say
     * @param string $msg
     * @return string
     * @soap
     */
    public function say($msg)
    {   
        $return = "Leeee ".$msg;
        return $return;
    }

   
}
