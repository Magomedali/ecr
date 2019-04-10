<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;


Modal::begin([
	'header'=>"<h4>Введите пароль от учетной записи</h4>",
	'id'=>$id
]);

	$invalidClass =  $inValidPassword ? 'invalidPassword' :'';
?>
<div id="<?php echo $id?>Content">
	<div class="row">
		<div class="col-md-12">
			<div class="form-group <?php echo $inValidPassword ? 'has-error' :'';?>">
				<label>Пароль</label>
				<?php echo Html::input('password',"password",null,['class'=>"form-control input-sm input_password {$invalidClass}",'required'=>false,'autocomplete'=>'off']);?>
				<?php if($inValidPassword){?>
					<p class="help-block help-block-error">Неправильный пароль</p>
				<?php } ?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<?php echo Html::submitButton("Подтвердить",['class'=>'btn btn-primary','id'=>$submitBtnId]);?>
		</div>
	</div>
</div>

<?php Modal::end();?>