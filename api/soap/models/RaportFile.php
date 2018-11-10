<?php

namespace api\soap\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\base\Model;

class RaportFile  extends ApiModel
{

    /**
     * @var base64binary
    */
    public $file;


    /**
    * @var string
    */
    public $type;


    /**
    * @var string
    */
    public $file_name;

}