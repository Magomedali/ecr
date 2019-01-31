<?php

namespace api\soap\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use yii\base\Model;

class ProjectStandard  extends ApiModel
{
    /**
     * @var string
    */
    public $project_guid;

    /**
    * @var string
    */
    public $typeofwork_guid;


    /**
    * @var float
    */
    public $standard;


}