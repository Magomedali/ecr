<?php

namespace api\soap\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\base\Model;

class Responce  extends Model
{
    /**
     * @var boolean
    */
    public $success;

    /**
    * @var string
    */
    public $error;

    /**
    * @var string
    */
    public $errorMessage;

    /**
    * @var string
    */
    public $errorUserMessage;

    /**
    * @var array
    */
    public $errorsExtend;

}
