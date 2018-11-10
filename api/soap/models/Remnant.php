<?php

namespace api\soap\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\base\Model;

class Remnant  extends ApiModel
{
    /**
     * @var string
    */
    public $brigade_guid;

    /**
    * @var datetime
    */
    public $updated_at;

    /**
     * @var string
    */
    public $nomenclature_guid;

    /**
     * @var float
    */
    public $count;

}