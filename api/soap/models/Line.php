<?php

namespace api\soap\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\base\Model;

class Line  extends ApiModel
{
    /**
     * @var string
    */
    public $guid;

    /**
    * @var string
    */
    public $name;

    /**
    * @var boolean
    * @nillable
    */
    public $is_countable;

    /**
    * @var string
    * @nillable
    */
    public $hint_count;

    /**
    * @var string
    * @nillable
    */
    public $hint_length;

}