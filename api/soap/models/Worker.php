<?php

namespace api\soap\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\base\Model;

class Worker  extends ApiModel
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
     * @nillable
    */
    public $brigade_guid;

    /**
    * @var string
    * @nillable
    */
    public $technic_guid;

    /**
     * @var float
    */
    public $ktu;

    /**
    * @var boolean
    */
    public $is_master;

    /**
    * @var string
    * @nillable
    */
    public $login;

    /**
    * @var string
    * @nillable
    */
    public $password;

}