<?php

namespace api\soap\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\base\Model;

class Technic  extends Model
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
    */
    public $marka;
    
    /**
     * @var string
    */
    public $number;

    

}