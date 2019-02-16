<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\widgets\docnotes;

use Yii;
use yii\base\Widget;


class DocNotes extends Widget
{   

    public static $autoIdPrefix = 'wNotes';

    public $notes = [];

    public $count = 0;
    


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

        return $this->view->renderFile($this->getViewPath()."/widget.php",[
            'notes'=>$this->notes,
            'count'=>$this->count
        ]);

    }

    /**
    * Register the needed assets
    */
    public function registerAssets(){

        $view = $this->getView();
        DocNotesAsset::register($view);
    }

}
