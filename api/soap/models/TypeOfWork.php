<?php

namespace api\soap\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\base\Model;

class TypeOfWork  extends ApiModel
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
    public $is_regulatory;


    /**
    * @var boolean
    * @nillable
    */
    public $req_percent_save;

    
    /**
    * @var string
    * @minOccurs 0
    * @maxOccurs unbounded
    */
    public $nomenclatures_guid;

}