<?php

namespace api\soap\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\base\Model;

class RaportConsist  extends ApiModel
{

    /**
     * @var string
    */
    public $technic_guid;

    /**
    * @var string
    */
    public $user_guid;
}