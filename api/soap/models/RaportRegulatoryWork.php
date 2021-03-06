<?php

namespace api\soap\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\base\Model;

class RaportRegulatoryWork  extends ApiModel
{

    /**
     * @var string
    */
    public $work_guid;

    /**
    * @var string
    */
    public $user_guid;

    /**
    * @var float
    */
    public $hours;
}