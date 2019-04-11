<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use common\dictionaries\ExchangeStatuses;
use common\models\User;
use common\widgets\autocomplete\AutoComplete;
use common\models\RaportRegulatoryWork;

$user = Yii::$app->user->identity;
$masters = User::find()->where(['is_master'=>true])->asArray()->all();

if(!$hasErrors){

	$RaportRegulatoryWorks =isset($model->id) ? $model->works : [[
		'work_guid'=>null,
		'work_name'=>null,
		'user_guid'=>null,
		'user_name'=>null,
		'hours'=>null
	]];
}else{
	$RaportRegulatoryWorks = $errorsRaportRegulatoryWork;
}




if(isset($model->id)){

	$master = isset($model->master_guid) ? $model->master : null;
	$master_name = isset($master->id) ? $master->name : null;
}else{
	if($hasErrors){
		$master_name = $errorsRaport['master_name'];
	}else{
		$master_name = "";
	}
}


$this->title = "Форма регламентного рапорта";
$this->params['backlink']['url']=Url::to(['raport/index']);
$this->params['backlink']['confirm']=true;
?>

<?php $form = ActiveForm::begin(['id'=>'raportRegulatoryForm']);?>
<div class="row">
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-12">

				<!-- Основное -->
				<div id="base">
					<div class="row">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-6">
									

									<?php echo $form->field($model,'created_at')->input("datetime-local",['value'=>isset($model->id) ? date("Y-m-d\TH:i:s",strtotime($model->created_at)) : date("Y-m-d\TH:i:s",time()),'readonly'=>true,'class'=>'form-control input-sm']); ?>



									<?php echo $form->field($model,'starttime')->input("time",['class'=>'form-control input-sm isRequired']); ?>

									<?php echo $form->field($model,'endtime')->input("time",['class'=>'form-control input-sm isRequired']);?>

									<?php if(isset($model->id)){ echo Html::hiddenInput('model_id',$model->id); }?>
									<?php 
										if(boolval($user->is_master)){
											echo $form->field($model,'brigade_guid')->hiddenInput()->label(false);
											echo $form->field($model,'user_guid')->hiddenInput()->label(false);
										} 
									?>
								</div>
								<div class="col-md-6">
									<?php 
									
										if(boolval($user->is_master) && $model->master_guid){
											echo Html::hiddenInput("RaportRegulatory[master_guid]",$model->master_guid);
											echo $form->field($model,'status')->dropDownList(ExchangeStatuses::getLabels());
										}else{
											echo AutoComplete::widget([
												'data'=>ArrayHelper::map($masters,'guid','name'),
												'apiUrl'=>Url::to(['/autocomplete/masters']),
												'inputValueName'=>'RaportRegulatory[master_guid]',
												'inputValueName_Value'=>$model->master_guid,
												'inputKeyName'=>'RaportRegulatory[master_name]',
												'inputKeyName_Value'=>$master_name,
												'placeholder'=>'Укажите мастера',
												'label'=>'Мастер'
											]);
										}
										
									?>
									<?php echo $form->field($model,'comment')->textarea(['class'=>'form-control input-sm','autocomplete'=>'off']);?>
									
								</div>
							</div>
						</div>
					</div>
				</div>

						


				<!-- Характеристики работ -->
				<div id="works">
					<h3>Характеристики работ</h3>
					<div class="row">
						<div class="col-md-12">
							<table id="tableWorks" class="table table-bordered table-hovered table-collapsed">
								<thead>
									<tr>
										<td>Физ.лицо</td>
										<td>Вид работы</td>
										<td>Количество часов</td>
										<td>
											<?php echo Html::a(
												'+',
												['raport-regulatory/get-row-work'],
												['class'=>'btn btn-sm btn-primary','id'=>'btnAddWork']
											);?>
										</td>
									</tr>
								</thead>
								<tbody>
									<?php if(is_array($RaportRegulatoryWorks)){?>
										<?php foreach ($RaportRegulatoryWorks as $key => $item) {?>
											<tr data-order="<?php echo $key?>">
												<td>
												<?php 
														echo AutoComplete::widget([
															'data'=>[],
															'apiUrl'=>Url::to(['/autocomplete/users']),
															'inputValueName'=>"RaportRegulatoryWork[$key][user_guid]",
															'inputValueName_Value'=>$item['user_guid'],
															'inputKeyName'=>"RaportRegulatoryWork[$key][user_name]",
															'inputKeyName_Value'=>$item['user_name'],
															'placeholder'=>'Укажите физ.лицо',
															'label'=>'Физ.лицо',
															'labelShow'=>false
														]);
													?>
												</td>
												<td class="td_work_guid">
													<?php 
														echo AutoComplete::widget([
															'data'=>[],
															'apiUrl'=>Url::to(['/autocomplete/works']),
															'inputValueName'=>"RaportRegulatoryWork[$key][work_guid]",
															'inputValueName_Value'=>$item['work_guid'],
															'inputKeyName'=>"RaportRegulatoryWork[$key][work_name]",
															'inputKeyName_Value'=>$item['work_name'],
															'placeholder'=>'Укажите вид работы',
															'labelShow'=>false,
															'label'=>'Вид работы',
															'generateSearchFiltersCallback'=>"function(){
																return {
																	extends:{
																		is_regulatory:1
																	}
																}
															}"
														]);
													?>
												</td>
												<td  class="td_hours">
													<?php 
														echo Html::input("number","RaportRegulatoryWork[$key][hours]",
															$item['hours'],
															[
															'class'=>'form-control input-sm isRequired',
															'step'=>"0.01",
															'autocomplete'=>'off',
															]
														); 
													?>
												</td>
												<td>
													<?php echo html::a('-',null,['class'=>'btn btn-sm btn-danger btnRemoveRow']);?>
												</td>
											</tr>
										<?php } ?>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php 
	if(!$user->is_master){
		$inValidPassword = isset($inValidPassword) ? $inValidPassword : false;
		echo \common\widgets\reqpasspresubmit\ReqPassPreSubmit::widget([
			'inValidPassword'=>$inValidPassword,
			'id'=>'modalPassword',
			'formId'=>'raportRegulatoryForm',
			'submitBtnId'=>'submitBtnId'
		]);
	}
	
?>
<div class="row">
	<div class="col-md-3">
		<?php echo Html::submitButton("Отправить",['id'=>'btnRaportFormSubmitPassword','class'=>'btn btn-primary']);?>
	</div>
</div>

<?php ActiveForm::end();?>

<?php 

$script = <<<JS
		

		var requiredFields = [
			"input.autocomplete_required",
			"input.isRequired"
		];

		var behaviorWhenSuccess = function(input){
			if(input.hasClass('autocomplete_required')){
				var val_input = input.siblings("input.autocomplete_input_value");
				if(val_input.val()){
					val_input.removeClass("fieldHasError");
					input.removeClass("fieldHasError");
					input.addClass("fieldIsSuccess");
				}
			}else{
				input.removeClass("fieldHasError");
				input.addClass("fieldIsSuccess");
			};
		};

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
							behaviorWhenSuccess(fieldForm);
						}
					})

				});
			}

			return !hasError;
		}

	validateRaportForm();
	
	//form submit
	$("form#raportRegulatoryForm").submit(function(event){
	    
	    $("#btnRaportFormSubmitPassword").prop("disabled",true);
		$("#submitBtnId").prop("disabled",true);

		if(!validateRaportForm()){
			event.preventDefault();
	        $("#btnRaportFormSubmitPassword").prop("disabled",false);
	        $("#submitBtnId").prop("disabled",false);
		}else if(typeof pluginReqPassPreSubmit == 'object'){
			if(!pluginReqPassPreSubmit.checkPasswordWindowIsOpen()){
				pluginReqPassPreSubmit.openWindow();
				event.preventDefault();
				$("#btnRaportFormSubmitPassword").prop("disabled",false);
				$("#submitBtnId").prop("disabled",false);
			}else if(!pluginReqPassPreSubmit.checkValidatePassword()){
				event.preventDefault();
				$("#btnRaportFormSubmitPassword").prop("disabled",false);
				$("#submitBtnId").prop("disabled",false);
			}
		}
	});


	//handler click on buttons for add form row
	var sendGetRowWork = 0;
	$("#btnAddWork").click(function(event){
		event.preventDefault();
		var action = $(this).attr("href");
		var table = $(this).parents("table");
		if(!table.length) return;
		
		var count = 0;
		if(table.find("tbody tr").length){
			var last_tr = table.find("tbody tr").eq(-1);
			var order = parseInt(last_tr.attr("data-order"));
			count = order >= 0 ? order + 1 : 0;
		}
		

		if(action && !sendGetRowWork){
			$.ajax({
				url:action,
				type:"GET",
				data:{count:count},
				dataType:'json',
				beforeSend:function(){
					$("#btnAddWork").prop("disabled",true);
					sendGetRowWork = 1;
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
					$("#btnAddWork").prop("disabled",false);
					sendGetRowWork = 0;
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


$this->registerJs($script);
?>
