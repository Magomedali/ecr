<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Сброс пароля';
?>
    <div class="row">
        <div class="col-md-5">
            <div class="panel panel-default">
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
                            <?php echo Html::submitButton('Сбросить', ['class' => 'btn btn-success', 'name' => 'password-reset-button']) ?>
                        </div>
                    </fieldset>
                     <?php ActiveForm::end(); ?>
                </div>
             </div>
         </div>
    </div>

