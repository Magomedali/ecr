<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\widgets\reqpasspresubmit;

use Yii;
use yii\base\Widget;

/**
* example 
*   echo \common\widgets\reqpasspresubmit\ReqPassPreSubmit::widget([
*        'inValidPassword'=>$inValidPassword,
*        'id'=>'modalPassword',
*        'formId'=>'materialForm',
*        'confirmCallback'=>'function(){$("#materialForm").trigger("submit");}'
*    ]);
*/
class ReqPassPreSubmit extends Widget
{   

    public $id = "widgetPassword";


    public $inValidPassword = false;

    public $formId = "null";

    public $confirmCallback = "null";
   


    /**
     * @inheritdoc
     */
    public function run()
    {   
        $this->registerAssets();
        return $this->renderWidget();
    }



    /**
     * Renders the AutoComplete widget.
     * @return string the rendering result.
     */
    public function renderWidget(){

        return $this->view->renderFile($this->getViewPath()."/widget.php",[
            'id'=>$this->id,
            'inValidPassword'=>$this->inValidPassword,
        ]);

    }

    /**
    * Register the needed assets
    */
    public function registerAssets(){

        $view = $this->getView();
        ReqPassPreSubmitAsset::register($view);
        $this->registerPlugin();
    }



    /**
    * Register the needed assets
    */
    public function registerPlugin(){

        $JS = <<<JS
            var pluginReqPassPreSubmit = {
                validePassword : function(){

                    var i = $("#{$this->id}Content .input_password");
                    if(!i.length) return false;

                    if(i.val().length < 6) return false;

                    return true;
                },

                checkValidatePassword : function(){

                    var res = pluginReqPassPreSubmit.validePassword();

                    if(!res){
                        $("#{$this->id}Content .input_password").addClass('invalidPassword');
                    }else{
                        $("#{$this->id}Content .input_password").removeClass('invalidPassword');
                    }

                    return res;
                },

                checkPasswordWindowIsOpen : function(){
                    var w = $("#{$this->id}");
                    return w.css("display") != "none";
                },

                openWindow:function(){
                    $("#{$this->id}").modal("show");
                    $("#{$this->id}Content .input_password").val(null);
                }
            };

            var confirmCallback = {$this->confirmCallback};
            var formId = '{$this->formId}';
            $("body").on("click","#btnConfirmPassword",function(){
                if(typeof confirmCallback == 'function'){
                    confirmCallback();
                }else{
                    var form = formId ? $("#"+formId) : $("form");
                    form.trigger("submit");
                }
                
            });
JS;

        $this->getView()->registerJs($JS);
    }

}
