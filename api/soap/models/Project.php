<?php

namespace api\soap\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\base\Model;

class Project  extends ApiModel
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
    * @var string
    * @minOccurs 1
    * @maxOccurs unbounded
    */
    public $objects_guids;

}