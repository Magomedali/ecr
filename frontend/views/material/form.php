<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\widgets\autocomplete\AutoComplete;

$this->title = "Заявка на получения материала";

if(!$hasErrors){
	$materialsAppItem = isset($model->id) ? $model->getMaterialsAppItems() : [];
}else{
	$materialsAppItem = $errorsMaterialsAppItem;
}

if(isset($model->id)){
	$stockroom = $model->stockroom_guid ? $model->stockroom : null;
	$stockroom_name = isset($stockroom->id) ? $stockroom->name : "";
}else{
	$stockroom = null;
	if($hasErrors){
		$stockroom_name = $errorsMaterialsApp['stockroom_name'];
	}else{
		$stockroom_name = "";
	}
}

?>

<div class="row">
	<div class="col-md-12">
		<?php $form = ActiveForm::begin(['id'=>'materialForm']);?>
		<div class="row">
			<div class="col-md-4">
				<?php echo $form->field($model,'created_at')->input("datetime-local",['value'=>isset($model->id) ? date("Y-m-d\TH:i:s",strtotime($model->created_at)) : date("Y-m-d\TH:i:s",time()),'readonly'=>true,'class'=>'form-control input-sm']); ?>
			</div>
			<div class="col-md-4">
				<?php 
					echo AutoComplete::widget([
						'data'=>ArrayHelper::map([],'guid','name'),
						'apiUrl'=>Url::to(['/autocomplete/stockroom']),
						'inputValueName'=>'MaterialsApp[stockroom_guid]',
						'inputValueName_Value'=>$model->stockroom_guid,
						'inputKeyName'=>'MaterialsApp[stockroom_name]',
						'inputKeyName_Value'=>$stockroom_name,
						'placeholder'=>'Выберите склад',
						'label'=>'Склад'
					]);
				?>
			</div>
			<div class="col-md-2" style="padding-top:24px; ">
				<?php if(isset($model->id)){ echo Html::hiddenInput('model_id',$model->id); }?>
				<?php echo Html::submitButton("Отправить",['id'=>'btnMaterialFormSubmit','class'=>'btn btn-primary'])?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<table id="tableMaterials" class="table table-bordered table-hovered table-collapsed">
					<thead>
						<tr>
							<td>Номенклатура</td>
							<td>Количество</td>
							<td><?php echo html::a('+',['material/get-row-material'],['class'=>'btn btn-sm btn-primary','id'=>'btnAddMaterial'])?></td>
						</tr>
					</thead>
					<tbody>
						<?php if(is_array($materialsAppItem)){
								foreach ($materialsAppItem as $key => $item) {
									if(!array_key_exists('count', $item) || !array_key_exists('nomenclature_name', $item) || !array_key_exists('nomenclature_guid', $item)) continue;
											?>
												<tr>
													<td>
													<?php 
														echo AutoComplete::widget([
															'data'=>[],
															'userId'=>"wNomenclatureGuid_$key",
															'apiUrl'=>Url::to(['/autocomplete/nomenclature']),
															'inputValueName'=>"MaterialsAppItem[$key][nomenclature_guid]",
															'inputValueName_Value'=>$item['nomenclature_guid'],
															'inputKeyName'=>"MaterialsAppItem[$key][nomenclature_name]",
															'inputKeyName_Value'=>$item['nomenclature_name'],
															'placeholder'=>'Номенклатура',
															'labelShow'=>false,
															
															'generateSearchFiltersCallback'=>"function(){
																
																var ns = $('#tableMaterials').find('input[name$=\'[nomenclature_guid]\'][name^=\'MaterialsAppItem\']');

																if(ns.length){
																	var data = [];
																	ns.each(function(){
																		data.push($(this).val());
																	});

																	return {
																		extends:data
																	}

																}else{
																	return {};
																}
															}"
														]);
													?>
													</td>
													<td>
														<?php echo Html::input("number","MaterialsAppItem[{$key}][count]",$item['count'],['min'=>0,'step'=>'0.001','class'=>'form-control input-sm isRequired'])?>
													</td>
													<td><?php echo html::a('-',null,['class'=>'btn btn-sm btn-danger btnRemoveRow'])?></td>
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
			"input.isRequired"
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



		//form submit
		$("form#materialForm").submit(function(event){
		    $("#btnMaterialFormSubmit").prop("disabled",true);
			if(!validateRaportForm()){
				event.preventDefault();
		        $("#btnMaterialFormSubmit").prop("disabled",false);
			}
		});



		var sendGetMaterialRom = 0;
		$("#btnAddMaterial").click(function(event){
			event.preventDefault();
			var action = $(this).attr("href");
			var table = $(this).parents("table");
			if(!table.length) return;
			var count = table.find("tbody tr").length;
			if(action && !sendGetMaterialRom){
				$.ajax({
					url:action,
					type:"GET",
					data:{count:count},
					dataType:'json',
					beforeSend:function(){
						sendGetMaterialRom = 1;
					},
					success:function(json){
						if(json.hasOwnProperty("html")){
							table.find("tbody").append(json.html);
						}
					},
					error:function(msg){
						console.log(msg);
					},
					complete:function(){
						sendGetMaterialRom = 0;
					}
				});
			}
		});


		//handler click on remove row buttons
		$("body").on("click",'.btnRemoveRow',function(event){
			event.preventDefault();

			var table = $(this).parents("table");
			if(table.find("tbody tr").length > 1){
				var tr = $(this).parents("tr");
				if(tr.length) tr.remove();
			}
			
		});

JS;

		$this->registerJs($js);
		?>

		<?php ActiveForm::end();?>
	</div>
</div>



