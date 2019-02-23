<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\models\User;
use common\models\{Nomenclature,DocumentTransfer};

$this->title = $doc['type_of_operation'];
$this->params['backlink']['url']=Url::to(['material/index']);
$this->params['backlink']['confirm']=true;
?>
<div class="row">
	<div class="col-md-12">
		<?php $form = ActiveForm::begin(['id'=>'formCommitDocument','action'=>['document/form']]);?>
		<div class="row">
			<div class="col-md-3 form-group">
				<label>Дата создания документа:</label>
				<?php echo Html::input("date",'doc[date]',$doc['date'],['class'=>'form-control','readonly'=>true]);?>
			</div>
			<div class="col-md-3 form-group">
				<label>Номер:</label>
				<?php echo Html::input("text",'doc[number]',$doc['number'],['class'=>'form-control','readonly'=>true]);?>
			</div>
			<div class="col-md-3 form-group">
				<label>Статус:</label>
				<?php 
					echo Html::input("text",'doc[status]',$doc['status'],['class'=>'form-control','readonly'=>true]);
				?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 form-group">
				<?php
					if(isset($doc['interaction_name']) && $doc['interaction_name']){
						?>
						<p><strong>От:</strong></p>
						<p><?php echo $doc['interaction_name'];?></p>
						<?php if(isset($doc['comment_interaction'])){?>
							<p><strong>Комментарии:</strong></p>
							<p><?php echo $doc['comment_interaction'];?></p>
						<?php } ?>
						<?php
					}
				?>
			</div>
			<div class="col-md-3 form-group">
				<label>Вид движения:</label>
				<?php
					echo Html::textInput('movement_type',$doc['movement_type'],['class'=>'form-control','readonly'=>true]);
				?>
			</div>
			<div class="col-md-3 form-group">
				<label>Комментарии:</label>
				<?php
					echo Html::textarea('doc[comment]',$doc['comment'],['class'=>'form-control','readonly'=>false]);
				?>
			</div>
		</div>
		<?php
			if($doc instanceof DocumentTransfer && count($remnants)){
		?>
		<div class="row">
			<div class="col-md-12" style="margin-bottom: 5px;">
				<div class="btn-group btn-group-sm pull-right" role="group" aria-label="Basic example">
					<?php 
						echo Html::button("Передать все",['class'=>'btn btn-default','id'=>'transferAllMaterials']);
						echo Html::button("Очистить",['class'=>'btn btn-default','id'=>'transferResetMaterials']);
					?>
				</div>
				<?php echo Html::hiddenInput("doc[mol_guid_recipient]",$mol_guid);?>
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

		var enableValidateCheck = true;


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

		
		if($("#documentCancel").length){
			$("#documentCancel").click(function(event){
				enableValidateCheck = false;
				$("#cancelInput").val(1);
			});
		}

		if($("#documentConfirm").length){
			$("#documentConfirm").click(function(event){
				enableValidateCheck = true;
				$("#cancelInput").val(0);
			});
		}

		//form submit
		$("form#formCommitDocument").submit(function(event){
			
			$("#documentConfirm").prop("disabled",true);
			
			if(enableValidateCheck && !checkRemnants()){
				event.preventDefault();
		        $("#documentConfirm").prop("disabled",false);
			}else if(typeof pluginReqPassPreSubmit == 'object'){
				if(!pluginReqPassPreSubmit.checkPasswordWindowIsOpen()){
					pluginReqPassPreSubmit.openWindow();
					event.preventDefault();
					$("#documentConfirm").prop("disabled",false);
				}else if(!pluginReqPassPreSubmit.checkValidatePassword()){
					event.preventDefault();
					$("#documentConfirm").prop("disabled",false);
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

		<?php }else{ ?>
		<div class="row">
			<div class="col-md-12">
				<table class="table table-sm table-bordered table-collapsed table-hovered">
					<tr>
						<th>Номенклатура</th>
						<th>Серия</th>
						<th>Количество</th>
					</tr>
					<?php
						if(isset($doc['materials']) && is_array($doc['materials'])){

							$materials = ArrayHelper::isAssociative($doc['materials']) ? array($doc['materials']) : $doc['materials'];

							foreach ($materials as $key => $item) {
					?>
						<tr>
							<td>
								<?php
									$nomen = Nomenclature::findOne(['guid'=>$item['nomenclature_guid']]);
									echo $nomen && isset($nomen->id) ? $nomen->name : '';
								?>
							</td>
							<td><?php echo $item['series_name'];?></td>
							<td><?php echo $item['count'];?></td>
						</tr>
					<?php
							}
						}
					?>
				</table>
			</div>
		</div>

		<?php
			$js = <<<JS


				if($("#documentCancel").length){
					$("#documentCancel").click(function(event){
						$("#cancelInput").val(1);
					});
				}

				if($("#documentConfirm").length){
					$("#documentConfirm").click(function(event){
						$("#cancelInput").val(0);
					});
				}

				$("form#formCommitDocument").submit(function(){
					if(typeof pluginReqPassPreSubmit == 'object'){
						if(!pluginReqPassPreSubmit.checkPasswordWindowIsOpen()){
							pluginReqPassPreSubmit.openWindow();
							event.preventDefault();
							$("#documentConfirm").prop("disabled",false);
						}else if(!pluginReqPassPreSubmit.checkValidatePassword()){
							event.preventDefault();
							$("#documentConfirm").prop("disabled",false);
						}
					}
				})
JS;
			$this->registerJs($js);
		?>

		<?php } ?>
		<div class="row">
			<div class="col-md-6 form-group">
				<?php 
					$disable = $doc instanceof DocumentTransfer && !count($remnants);
					echo Html::hiddenInput("doc[guid]",$doc['guid']);
					echo Html::hiddenInput("doc[movement_type]",$doc['movement_type']);
					echo Html::hiddenInput("doc[type_of_operation]",$doc['type_of_operation']);
					echo Html::hiddenInput("cancel",0,['id'=>'cancelInput']);
				?>
				<div class="btn-group" role="group" aria-label="Basic example">
					<?php 
						echo Html::submitButton('Подтвердить',['name'=>'commitbtn','class'=>'btn btn-success','id'=>"documentConfirm",'disabled'=>$disable]);
						echo Html::submitButton('Отменить',['name'=>'cancelbtn','class'=>'btn btn-danger','id'=>"documentCancel"]);
					?>
				</div>
			</div>
		</div>

		<?php 
			$inValidPassword = isset($inValidPassword) ? $inValidPassword : false;
			echo \common\widgets\reqpasspresubmit\ReqPassPreSubmit::widget([
				'inValidPassword'=>$inValidPassword,
				'id'=>'modalPassword',
				'formId'=>'formCommitDocument'
			]);
		?>
		<?php ActiveForm::end();?>
	</div>
</div>
