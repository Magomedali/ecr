<?php

namespace api\soap\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\base\Model;

class Responce  extends ApiModel
{
    /**
     * @var boolean
    */
    public $success = false;

    /**
    * @var string
    * @nillable
    */
    public $error;

    /**
    * @var string
    * @nillable
    */
    public $errorMessage;

    /**
    * @var string
    * @nillable
    */
    public $errorUserMessage;

    /**
    * @var array
    * @nillable
    */
    public $errorsExtend;


}
