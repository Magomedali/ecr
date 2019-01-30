<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\widgets\autocomplete\AutoComplete;

$this->title = "Перевод материалов на другого бригадира";

$stockroom = null;
if($hasErrors){
	$mol_guid_recipient_name = $errorsTransfer['mol_guid_recipient_name'];
}else{
	$mol_guid_recipient_name = "";
}


?>

<div class="row">
	<div class="col-md-12">
		<?php $form = ActiveForm::begin(['id'=>'transferMaterialForm']);?>
		<div class="row">
			<div class="col-md-4">
				<?php echo $form->field($model,'created_at')->input("datetime-local",['value'=>isset($model->created_at) && $model->created_at ? date("Y-m-d\TH:i:s",strtotime($model->created_at)) : date("Y-m-d\TH:i:s",time()),'readonly'=>true,'class'=>'form-control input-sm']); ?>
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
			<div class="col-md-2" style="padding-top:24px; ">
				<?php echo Html::submitButton("Отправить",['id'=>'btnMaterialFormSubmit','class'=>'btn btn-primary'])?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<?php echo $form->field($model,'comment')->textarea(); ?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12" style="margin-bottom: 5px;">
				<?php 
					echo Html::a("Очистить",null,['class'=>'btn btn-danger pull-right','id'=>'transferResetMaterials']);
					echo Html::a("Передать все",null,['class'=>'btn btn-primary pull-right','id'=>'transferAllMaterials']);
				?>
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

								<?php echo Html::textInput("materials[{$key}][nomenclature_name]",$item['nomenclature_name'],['class'=>'form-control input-sm isRequired','readonly'=>true])?>

							</td>
							<td>
								<?php echo Html::hiddenInput("materials[{$key}][series_guid]",$item['series_guid'],['class'=>'form-control input-sm isRequired'])?>
								<?php echo Html::textInput("materials[{$key}][series_name]",$item['series_name'],['class'=>'form-control input-sm isRequired','readonly'=>true])?>
							</td>
							<td>
								<?php echo Html::input("number","materials[{$key}][count]",$item['count'],['min'=>0,'step'=>'0.001','class'=>'form-control was_input input-sm','readonly'=>true])?>
							</td>
							<td>
								<?php echo Html::input("number","materials[{$key}][send]",isset($item['send']) ? $item['send'] : null,['min'=>0,'step'=>'0.001','max'=>$item['count'],'class'=>'form-control input-sm spent_input isRequired'])?>
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

		//form submit
		$("form#transferMaterialForm").submit(function(event){
			$("#btnMaterialFormSubmit").prop("disabled",true);
			
			if(!validateRaportForm() || !checkRemnants()){
				event.preventDefault();
		        $("#btnMaterialFormSubmit").prop("disabled",false);
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
			var r = parseFloat(total - value);
	    	rest.val(r.toFixed(3));
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

		<?php ActiveForm::end();?>
	</div>
</div>



