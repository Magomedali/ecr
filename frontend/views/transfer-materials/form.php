<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\widgets\autocomplete\AutoComplete;
use common\models\Nomenclature;
use common\models\User;

$this->title = "Перевод материалов на другого бригадира";
$this->params['backlink']['url']=Url::to(['material/index']);
$this->params['backlink']['confirm']=true;

if($hasErrors){
	$mol_guid_recipient_name = $errorsTransfer['mol_guid_recipient_name'];
}else{

	if($model->mol_guid_recipient){
		$mol = User::findOne(['guid'=>$model->mol_guid_recipient]);
		$mol_guid_recipient_name = isset($mol->name) ? $mol->name : "Неизвестный мол";
	}else{
		$mol_guid_recipient_name = "";
	}
}


?>

<div class="row">
	<div class="col-md-12">
		<?php $form = ActiveForm::begin(['id'=>'transferMaterialForm']);?>
		<div class="row">
			<div class="col-md-4">
				<?php echo $form->field($model,'date')->input("datetime-local",['value'=>isset($model->date) && $model->date ? date("Y-m-d\TH:i:s",strtotime($model->date)) : date("Y-m-d\TH:i:s",time()),'readonly'=>true,'class'=>'form-control input-sm']); ?>
			</div>
			<div class="col-md-4">
				<?php 
					echo AutoComplete::widget([
						'data'=>[],
						'apiUrl'=>Url::to(['/autocomplete/brigadier']),
						'inputValueName'=>'TransferMaterials[mol_guid_recipient]',
						'inputValueName_Value'=>$model->mol_guid_recipient,
						'inputKeyName'=>'TransferMaterials[mol_guid_recipient_name]',
						'inputKeyName_Value'=>$mol_guid_recipient_name,
						'placeholder'=>'Выберите бригадира',
						'label'=>'Бригадир'
					]);
				?>
			</div>
			<div class="col-md-4" style="padding-top:24px; ">
				<div class="btn-group pull-right" role="group" aria-label="Basic example">
				<?php echo Html::submitButton("Отправить",['id'=>'btnMaterialFormSubmit','class'=>'btn btn-primary'])?>
					<?php 
						if($request){
							echo Html::hiddenInput("cancel",0,['id'=>'cancelInput']);
							echo Html::submitButton("Отменить",['id'=>'transferMaterialCancel','name'=>'transferMaterialCancel','class'=>'btn btn-danger']);
						}
					?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<?php echo $form->field($model,'comment')->textarea(); ?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12" style="margin-bottom: 5px;">
				<div class="btn-group btn-group-sm pull-right" role="group" aria-label="Basic example">
					<?php 
						echo Html::button("Передать все",['class'=>'btn btn-default','id'=>'transferAllMaterials']);
						echo Html::button("Очистить",['class'=>'btn btn-default','id'=>'transferResetMaterials']);
					?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<table id="tableMaterials" class="table table-bordered table-hovered table-collapsed">
					<thead>
						<tr>
							<td>Номенклатура</td>
							<td>Серия</td>
							<td>Количество</td>
							<td>Передать</td>
							<td>Остается</td>
						</tr>
					</thead>
					<tbody>
						<?php if(is_array($remnants)){
								foreach ($remnants as $key => $item) {
							?>
						<tr>
							<td>
								<?php echo Html::hiddenInput("materials[{$key}][nomenclature_guid]",$item['nomenclature_guid'],['class'=>'form-control input-sm isRequired'])?>

								<?php 
									if(!isset($item['nomenclature_name'])){
										$nomen = Nomenclature::findOne(['guid'=>$item['nomenclature_guid']]);
										$nomenclature_name = $nomen && isset($nomen->name) ? $nomen->name : "";
									}else{
										$nomenclature_name = $item['nomenclature_name'];
									}
									echo Html::textInput("materials[{$key}][nomenclature_name]",$nomenclature_name,['class'=>'form-control input-sm isRequired','readonly'=>true]);
								?>
							</td>
							<td>
								<?php echo Html::hiddenInput("materials[{$key}][series_guid]",$item['series_guid'],['class'=>'form-control input-sm isRequired'])?>
								<?php echo Html::textInput("materials[{$key}][series_name]",$item['series_name'],['class'=>'form-control input-sm isRequired','readonly'=>true])?>
							</td>
							<td>
								<?php echo Html::input("number","materials[{$key}][count]",$item['count'],['min'=>0,'step'=>'0.001','class'=>'form-control was_input input-sm','readonly'=>true])?>
							</td>
							<td>
								<?php 
									$sended = null;
									
									if(is_array($unLoadedMaterials) && count($unLoadedMaterials) 
										&& array_key_exists($item['nomenclature_guid'], $unLoadedMaterials)
										&& array_key_exists($item['series_guid'], $unLoadedMaterials[$item['nomenclature_guid']])
										&& array_key_exists('count', $unLoadedMaterials[$item['nomenclature_guid']][$item['series_guid']])){

										$sended = $unLoadedMaterials[$item['nomenclature_guid']][$item['series_guid']]['count'];
									}elseif(isset($item['send'])){
										$sended = $item['send'];
									}

									echo Html::input("number","materials[{$key}][send]",$sended,['min'=>0,'step'=>'0.001','max'=>$item['count'],'class'=>'form-control input-sm spent_input isRequired']);
								?>
							</td>
							<td>
								<?php echo Html::input("number","materials[{$key}][rest]",isset($item['rest']) ? $item['rest'] : $item['count'],['min'=>0,'step'=>'0.001','class'=>'form-control rest_input input-sm','readonly'=>true])?>
							</td>
						</tr>
						<?php
								}
							}
						?>
					</tbody>
				</table>
			</div>
		</div>

		<?php


		$js = <<<JS

		var requiredFields = [
			"input.autocomplete_required",
		];

		var enableValidateCheck = true;

		var validateRaportForm = function(){

			var hasError = false;

			if(requiredFields.length){
				$.each(requiredFields,function(i,field){
					var fieldsForms = $(field);

					if(!fieldsForms.length) return;

					fieldsForms.each(function(){
						var fieldForm = $(this);
						
						if(!fieldForm.val()){
							hasError = true;

							fieldForm.addClass("fieldHasError");
							
							//hide p.help-block
							var pHelpBlock = fieldForm.siblings("p.help-block");
							pHelpBlock.length ? pHelpBlock.remove() : null;
						}else{
							fieldForm.removeClass("fieldHasError");
						}
					})

				});
			}

			return !hasError;
		}

		var checkRemnants = function(){
			var fields = $("input[name^=materials][name$='[send]']");
			var valid = false;
			if(!fields.length) return false;

			fields.each(function(){
				if($(this).val() > 0) valid = true;
			});
			
			if(valid){
				fields.removeClass("fieldHasError");
			}else{
				fields.addClass("fieldHasError");
			}

			return valid;
		}

		
		if($("#transferMaterialCancel").length){
			$("#transferMaterialCancel").click(function(event){
				enableValidateCheck = false;
				var cancelInput = $("#cancelInput");
				if(cancelInput.length) cancelInput.val(1);
			});
		}

		$("#btnMaterialFormSubmit").click(function(event){
			enableValidateCheck = true;
			var cancelInput = $("#cancelInput");
			if(cancelInput.length) cancelInput.val(0);
		});

		//form submit
		$("form#transferMaterialForm").submit(function(event){
			// submit more than once return false
			
			$("#btnMaterialFormSubmit").prop("disabled",true);
			
			if(enableValidateCheck && (!validateRaportForm() || !checkRemnants())){
				event.preventDefault();
		        $("#btnMaterialFormSubmit").prop("disabled",false);
			}else if(typeof pluginReqPassPreSubmit == 'object'){
				if(!pluginReqPassPreSubmit.checkPasswordWindowIsOpen()){
					pluginReqPassPreSubmit.openWindow();
					event.preventDefault();
					$("#btnMaterialFormSubmit").prop("disabled",false);
				}else if(!pluginReqPassPreSubmit.checkValidatePassword()){
					event.preventDefault();
					$("#btnMaterialFormSubmit").prop("disabled",false);
				}
			}
		});


		$('body').on("click","#transferAllMaterials",function(event){
			event.preventDefault();
			var sends = $("#tableMaterials input.spent_input");

			if(!sends.length) return false;

			sends.each(function(){
				var count = $(this).parents("tr").find("input.was_input");
				if(count.length){
					$(this).val(count.val());
				}
			});
		});

		$('body').on("click","#transferResetMaterials",function(event){
			event.preventDefault();
			var sends = $("#tableMaterials input.spent_input");

			if(sends.length) sends.val(null);

		});


		$("body").on("change",".spent_input",function(){
			var rest = $(this).parents("tr").find(".rest_input");
			var total = parseFloat($(this).attr("max"));
			var value = parseFloat($(this).val());
			if(value){
				var r = parseFloat(total - value);
	    		rest.val(r.toFixed(3));
			}else{
				rest.val(total);
			}
			
		});

		$("body").on("keyup",".spent_input",function(){
			var rest = $(this).parents("tr").find(".rest_input");
			
			var total = parseFloat($(this).attr("max"));
			var min = parseFloat($(this).attr("min"));
			var value = parseFloat($(this).val());
	        
			if(min > value){
				$(this).val(min);
				value = min;
			}else if(total < value){
				$(this).val(total);
				value = total;
			}

			if(value || value === 0){
				var rest_value = parseFloat(total - value);
				rest.val(rest_value.toFixed(3));
			}else{
				rest.val("");
			}
			
		});

JS;

		$this->registerJs($js);
		?>

<?php 
	$inValidPassword = isset($inValidPassword) ? $inValidPassword : false;
	echo \common\widgets\reqpasspresubmit\ReqPassPreSubmit::widget([
		'inValidPassword'=>$inValidPassword,
		'id'=>'modalPassword',
		'formId'=>'transferMaterialForm'
	]);
?>
		<?php ActiveForm::end();?>
	</div>
</div>



