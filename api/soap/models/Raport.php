<?php

namespace api\soap\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\base\Model;

class Raport  extends ApiModel
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
    * @var float
    * @nillable
    */
    public $temperature_start;

    /**
    * @var float
    * @nillable
    */
    public $temperature_end;

    /**
    * @var float
    * @nillable
    */
    public $surface_temperature_start;

    /**
    * @var float
    * @nillable
    */
    public $surface_temperature_end;

    /**
    * @var float
    * @nillable
    */
    public $airhumidity_start;

    /**
    * @var float
    * @nillable
    */
    public $airhumidity_end;

    /**
    * @var string
    */
    public $brigade_guid;

    /**
    * @var string
    */
    public $object_guid;

    /**
    * @var string
    */
    public $boundary_guid;

    /**
    * @var string
    */
    public $project_guid;

    /**
    * @var string
    */
    public $master_guid;

	/**
    * @var string
    * @nillable
    */
    public $comment;
    
    /**
    * @var api\soap\models\RaportFile[]
    * @nillable
    */
    public $files;

    /**
    * @var api\soap\models\RaportMaterial[]
    * @minOccurs 1
    */
    public $materials;

    /**
    * @var api\soap\models\RaportWork[]
    * @minOccurs 1
    */
    public $works;

    /**
    * @var api\soap\models\RaportConsist[]
    * @minOccurs 1
    */
    public $consist;
}