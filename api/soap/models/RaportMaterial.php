<?php

namespace api\soap\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\base\Model;

class RaportMaterial  extends ApiModel
{

    /**
     * @var string
    */
    public $nomenclature_guid;

    /**
    * @var float
    * @nillable
    */
    public $was;


    /**
    * @var float
    * @nillable
    */
    public $spent;

    /**
    * @var float
    */
    public $rest;

}