<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\widgets\autocomplete;

use Yii;
use yii\base\Widget;


class AutoComplete extends Widget
{   

    public static $autoIdPrefix = 'w';

    public $inputValueName = "autocomplete_input_name";

    public $inputKeyName = "autocomplete_input_key";


    public $inputValueName_Value = "";

    public $inputKeyName_Value = "";

    public $placeholder = 'Введите строку для поиска';

    public $label = "Label";

    public $labelShow = true;

    public $apiUrl = "";

    public $data = [];

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
            'id'=>$this->getId(),
            'data'=>$this->data,
            'inputValueName'=>$this->inputValueName,
            'inputKeyName'=>$this->inputKeyName,
            'apiUrl'=>$this->apiUrl,
            'placeholder'=>$this->placeholder,
            'inputValueName_Value'=>$this->inputValueName_Value,
            'inputKeyName_Value'=>$this->inputKeyName_Value,
            'label'=>$this->label,
            'labelShow'=>$this->labelShow
        ]);
    }

    /**
    * Register the needed assets
    */
    public function registerAssets(){
        
        $view = $this->getView();
        AutoCompleteAsset::register($view);
    }

}
