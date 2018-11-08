<?php

namespace api\soap\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\base\Model;

class Worker  extends Model
{
    /**
     * @var string
    */
    public $guid;

    /**
     * @var string
    */
    public $brigade_guid;

    /**
    * @var string
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
    */
    public $login;

    /**
    * @var boolean
    */
    public $password;

}