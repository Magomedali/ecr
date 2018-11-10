<?php

namespace api\soap\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\base\Model;

class RaportWork  extends ApiModel
{

    /**
     * @var string
    */
    public $work_guid;

    /**
    * @var string
    */
    public $line_guid;


    /**
    * @var boolean
    */
    public $mechanized;

    /**
    * @var float
    */
    public $length;

    /**
    * @var float
    */
    public $count;


    /**
    * @var float
    */
    public $squaremeter;
}