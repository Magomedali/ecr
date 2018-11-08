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
                'serviceOptions'=>[
                    'disableWsdlMode'=>true
                ]
            ]
        ];
    }



    /**
     * unload Brigade
     * @param api\soap\models\Brigade[] $brigades
     * @return string
     * 
     */
    public function unloadbrigade($brigades)
    {   
        return "Hello";
    }


    /**
     * Say
     * @param string $msg
     * @return string
     * @soap
     */
    public function say($msg)
    {   
        return "Hello ".$msg;
    }

   
}
