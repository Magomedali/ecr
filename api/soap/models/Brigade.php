<?php

namespace api\soap\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;


class Brigade  
{
    
    /**
     * @var string
    */
    public $guid;

    /**
    * @var string
    */
    public $name;

    public function __construct($name){

    }

}