<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\widgets\raportform;

use Yii;
use yii\base\Widget;


class RaportForm extends Widget
{   

    public static $autoIdPrefix = 'wForm';

    public $model;

    public $forManager = false;

    public $disableMaster = false;

    public $user;

    public $masters;

    public $BrigadeConsist;

    public $ActualBrigadeRemnants;

    public $RaportWorks;

    public $object_name = "";

    public $boundary_name = "";

    public $project_name = "";

    public $master_name = "";

    public $enableGuardPassword = true;

    public $inValidPassword = true;

    public $loadStandarsUrl;

    public $urlLoadRaportWorkRow;

    public $urlLoadRaportConsistRow;
    
    public $requiredFile = false;

    public $updateStatus = false;

    public $statuses = [];

    public $raportWorkPercents = [];

    /**
     * @inheritdoc
     */
    public function run()
    {   
        $idCount = $this->getId();
        
        $this->registerAssets();
        return $this->renderWidget();
    }



    /**
     * Renders the AutoComplete widget.
     * @return string the rendering result.
     */
    public function renderWidget(){

        return $this->view->renderFile($this->getViewPath()."/form.php",[
            'model'=>$this->model,
            'forManager'=>$this->forManager,
            'user'=>$this->user,
            'disableMaster'=>$this->disableMaster,
            'masters'=>$this->masters,
            'BrigadeConsist'=>$this->BrigadeConsist,
            'ActualBrigadeRemnants'=>$this->ActualBrigadeRemnants,
            'RaportWorks'=>$this->RaportWorks,
            'object_name'=>$this->object_name,
            'boundary_name'=>$this->boundary_name,
            'project_name'=>$this->project_name,
            'master_name'=>$this->master_name,
            'inValidPassword'=>$this->inValidPassword,
            'enableGuardPassword'=>$this->enableGuardPassword,
            'loadStandarsUrl'=>$this->loadStandarsUrl,
            'urlLoadRaportWorkRow'=>$this->urlLoadRaportWorkRow,
            'urlLoadRaportConsistRow'=>$this->urlLoadRaportConsistRow,
            'requiredFile'=>$this->requiredFile,
            'updateStatus'=>$this->updateStatus,
            'statuses'=>$this->statuses,
            'raportWorkPercents'=>$this->raportWorkPercents
        ]);

    }

    /**
    * Register the needed assets
    */
    public function registerAssets(){

        $view = $this->getView();
        RaportFormAsset::register($view);
    }

}
