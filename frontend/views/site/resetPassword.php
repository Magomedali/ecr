<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Сброс пароля';
?>
    <div class="row">
        <div class="col-md-5">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Сброс пароля</h3>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
                    <fieldset>
                        <?php echo $form->field($model, 'old_password')->textInput(['autofocus' => true]) ?>

                        <?php echo $form->field($model, 'password')->passwordInput() ?>

                        <?php echo $form->field($model, 'confirm_password')->passwordInput() ?>

                        <div class="form-group">
                            <?php echo Html::submitButton('Сбросить', ['class' => 'btn btn-lg btn-success btn-block', 'name' => 'login-button']) ?>
                        </div>
                    </fieldset>
                     <?php ActiveForm::end(); ?>
                </div>
             </div>
         </div>
    </div>

