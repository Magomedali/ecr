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

    public $userId = null;

    public $inputValueName = "autocomplete_input_name";

    public $inputKeyName = "autocomplete_input_key";

    public $required = true;

    public $inputValueName_Value = "";

    public $inputKeyName_Value = "";

    public $placeholder = 'Введите строку для поиска';

    public $label = "Label";

    public $labelShow = true;

    public $apiUrl = "";

    public $data = [];

    public $onSelectCallback="function(item){}";

    public $generateSearchFiltersCallback = "function(){return {};}";

    /**
    * array[]['property'=>'','commonElement'=>'','targetElement'=>'']
    *
    */
    public $properties = [];


    /**
    * array[]['name'=>'','valueFromElement']
    *
    */
    public $parameters = [];

    /**
    */
    public $options = [
        'minKeyLength' => 0,
        'searchOnFocusin'=>true,
    ];


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

        $id = $this->userId ? static::$autoIdPrefix . $this->userId : $this->getId();

        return $this->view->renderFile($this->getViewPath()."/widget.php",[
            'id'=>$id,
            'data'=>$this->data,
            'inputValueName'=>$this->inputValueName,
            'inputKeyName'=>$this->inputKeyName,
            'apiUrl'=>$this->apiUrl,
            'placeholder'=>$this->placeholder,
            'inputValueName_Value'=>$this->inputValueName_Value,
            'inputKeyName_Value'=>$this->inputKeyName_Value,
            'label'=>$this->label,
            'labelShow'=>$this->labelShow,
            'properties'=>$this->properties,
            'parameters'=>$this->parameters,
            'options'=>$this->options,
            'required'=>$this->required,
            'onSelectCallback'=>$this->onSelectCallback,
            'generateSearchFiltersCallback'=>$this->generateSearchFiltersCallback
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
