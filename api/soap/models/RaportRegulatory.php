<?php

namespace api\soap\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\base\Model;

class RaportRegulatory  extends ApiModel
{
    /**
     * @var string
    */
    public $guid;

    /**
    * @var string
    */
    public $number;


    /**
    * @var string
    */
    public $status;


    /**
    * @var datetime
    */
    public $created_at;


    /**
    * @var time
    */
    public $starttime;


    /**
    * @var time
    */
    public $endtime;


    /**
    * @var string
    */
    public $brigade_guid;


    /**
    * @var string
    */
    public $master_guid;

    /**
    * @var string
    */
    public $user_guid;

	/**
    * @var string
    * @nillable
    */
    public $comment;

    /**
    * @var api\soap\models\RaportRegulatoryWork
    * @minOccurs 1
    * @maxOccurs unbounded
    */
    public $works;


    /**
     * @var int 
     * @nillable
    */
    public $id_site;
}