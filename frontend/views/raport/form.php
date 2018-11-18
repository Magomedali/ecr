<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\models\User;
use common\widgets\autocomplete\AutoComplete;


$user = Yii::$app->user->identity;
$masters = User::find()->where(['is_master'=>true])->asArray()->all();
$BrigadeConsist = $user->brigadeConsist;
$ActualBrigadeRemnants = $user->actualBrigadeRemnants;


$this->title = "Форма рапорта";
?>
<?php $form = ActiveForm::begin();?>
<div class="row">
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-3">
				<?php 
					echo AutoComplete::widget([
						'data'=>ArrayHelper::map($masters,'guid','name'),
						'apiUrl'=>Url::to(['/autocomplete/masters']),
						'inputValueName'=>'Raport[master_guid]',
						'inputValueName_Value'=>"",
						'inputKeyName'=>'master_key',
						'inputKeyName_Value'=>"",
						'placeholder'=>'Укажите мастера',
						'label'=>'Мастер'
					]);
				?>
				<?php echo $form->field($model,'brigade_guid')->hiddenInput(['value'=>$user->brigade_guid])->label(false);?>
			</div>

			<div class="col-md-6">
				<?php echo Html::submitButton("Сохранить",['class'=>'btn btn-primary']);?>
			</div>
		</div>
		<div class="row">
				<div class="col-md-12">
					<ul class="nav nav-tabs">	
					  <li class="active"><a data-toggle="tab" href="#base">Основное</a></li>
					  <li><a data-toggle="tab" href="#consist">Состав бригады</a></li>
					  <li><a data-toggle="tab" href="#works">Характеристики работ</a></li>
					  <li><a data-toggle="tab" href="#remnants">Остатки</a></li>
					</ul>
					<div class="tab-content">

						<!-- Основное -->
						<div id="base" class="tab-pane fade in active">
							<h3>Основное</h3>
							<div class="row">
								<div class="col-md-12">
									<div class="row">
										<div class="col-md-2">
											<?php echo $form->field($model,'created_at')->input("date",['value'=>date("Y-m-d"),'disabled'=>true]);?>

											<?php echo $form->field($model,'starttime')->input("time");?>

											<?php echo $form->field($model,'endtime')->input("time");?>
										</div>

										<div class="col-md-3">
											<div class="row">
												<div class="col-md-6">
													<?php echo $form->field($model,'temperature_start')->input("number");?>
													<?php echo $form->field($model,'surface_temperature_end')->input("number");?>
													<?php echo $form->field($model,'airhumidity_start')->input("number");?>
												</div>
												<div class="col-md-6">
													<?php echo $form->field($model,'surface_temperature_start')->input("number");?>
													<?php echo $form->field($model,'temperature_end')->input("number");?><?php echo $form->field($model,'airhumidity_end')->input("number");?>
												</div>
											</div>
										</div>

										<div class="col-md-3 object_form">
											<div class="row">
												<div class="col-md-12">
													<?php 
														echo AutoComplete::widget([
															'data'=>[],
															'apiUrl'=>Url::to(['/autocomplete/objects']),
															'inputValueName'=>"Raport[object_guid]",
															'inputValueName_Value'=>"",
															'inputKeyName'=>'object_key',
															'inputKeyName_Value'=>"",
															'placeholder'=>'Укажите объект',
															'label'=>"Объект",
															'properties'=>[
																['property'=>'boundary_guid','commonElement'=>'div.object_form','targetElement'=>'input#raport-boundary_guid'],
																['property'=>'boundary_name','commonElement'=>'div.object_form','targetElement'=>'input.input_boundary_name']
															]
														]);
													?>
												</div>
												<div class="col-md-12">
													<?php 
														echo AutoComplete::widget([
															'data'=>[],
															'apiUrl'=>Url::to(['/autocomplete/projects']),
															'inputValueName'=>"Raport[project_guid]",
															'inputValueName_Value'=>"",
															'inputKeyName'=>'project_key',
															'inputKeyName_Value'=>"",
															'placeholder'=>'Укажите контракт',
															'label'=>"Контракт",
															'parameters'=>[
																['name'=>'object_guid','valueFromElement'=>"input[name=\"Raport[object_guid]\"]"]
															],
															'options'=>[
																'minKeyLength'=>0,
																'searchOnFocusin'=>true
															]
														]);
													?>
												</div>
												<div class="col-md-12">
													<label>Округ</label>
													<?php echo Html::textInput("boundary_name",null,['class'=>'form-control input_boundary_name','disabled'=>true]);?>
													
													<?php echo $form->field($model,'boundary_guid')->hiddenInput()->label(false);?>
												</div>
											</div>
										</div>

										<div class="col-md-3">
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label class="form-label">Прикрепить файлы:</label>
														<?php echo Html::fileInput("files[]",null,['multiple'=>true]);?>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<?php echo $form->field($model,'comment')->textarea();?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<!-- Состав бригады -->
						<div id="consist" class="tab-pane fade in">
							<h3>Состав бригады</h3>
							<div class="row">
								<div class="col-md-12">
									<table id="tableConsist" class="table table-bordered table-hovered table-collapsed">
										<thead>
											<tr>
												<td>#</td>
												<td>Физ.лицо</td>
												<td>Техника</td>
												<td>КТУ</td>
												<td><?php echo html::a('+',['raport/get-row-consist'],['class'=>'btn btn-sm btn-primary','id'=>'btnAddConsist'])?></td>
											</tr>
										</thead>
										<tbody>
											<?php if(is_array($BrigadeConsist)){
												foreach ($BrigadeConsist as $key => $item) {
											?>
												<tr>
													<td><?php echo 1+$key;?></td>
													<td>
													<?php 
														echo $item['name'];
														echo Html::hiddenInput("RaportConsist[$key][user_guid]",$item['guid']);
													?>
													</td>
													<td>
													<?php 
														echo $item['technic_name'];
														echo Html::hiddenInput("RaportConsist[$key][technic_guid]",$item['technic_guid']);
													?>
													</td>
													<td><?php echo $item['ktu'];?></td>
													<td><?php echo html::a('-',null,['class'=>'btn btn-sm btn-danger','id'=>'btnRemoveRow'])?></td>
												</tr>
											<?php
												}
											}
											?>
										</tbody>
									</table>
								</div>
							</div>
						</div>


						<!-- Характеристики работ -->
						<div id="works" class="tab-pane fade in">
							<h3>Характеристики работ</h3>
							<div class="row">
								<div class="col-md-12">
									<table id="tableWorks" class="table table-bordered table-hovered table-collapsed">
										<thead>
											<tr>
												<td>#</td>
												<td>Вид работы</td>
												<td>Линия</td>
												<td>Механизировання</td>
												<td>Длина</td>
												<td>Количество</td>
												<td>кв. м</td>
												<td><?php echo html::a('+',['raport/get-row-work'],['class'=>'btn btn-sm btn-primary','id'=>'btnAddWork'])?></td>
											</tr>
										</thead>
										<tbody>
											
										</tbody>
									</table>
								</div>
							</div>
						</div>



						<!-- Остатки -->
						<div id="remnants" class="tab-pane fade in">
							<h3>Остатки</h3>
							<div class="row">
								<div class="col-md-12">
									<table id="tableRemnants" class="table table-bordered table-hovered table-collapsed">
										<thead>
											<tr>
												<td>#</td>
												<td>Номенклатура</td>
												<td>Начальный остаток</td>
												<td>Израсходовано</td>
												<td>Конечный остаток</td>
											</tr>
										</thead>
										<tbody>
											<?php if(is_array($ActualBrigadeRemnants)){
												foreach ($ActualBrigadeRemnants as $key => $item) {
											?>
												<tr>
													<td><?php echo 1+$key;?></td>
													<td>
													<?php 
														echo $item['nomenclature_name'];
														echo Html::hiddenInput("RaportMaterial[$key][nomenclature_guid]",$item['nomenclature_guid']);
													?>
													</td>
													<td>
													<?php 
														echo Html::textInput("RaportMaterial[$key][was]",$item['count'],['class'=>'form-control was_input','disabled'=>1]);
													?>
													</td>
													<td>
													<?php 
														echo Html::input("number","RaportMaterial[$key][spent]",null,['class'=>'form-control spent_input','min'=>0,'max'=>$item['count']]);
													?>
													</td>
													<td>
														<?php 
															echo Html::textInput("RaportMaterial[$key][rest]",null,['class'=>'form-control rest_input','disabled'=>1]);
														?>
													</td
												</tr>
											<?php
												}
											}
											?>
										</tbody>
									</table>
								</div>
							</div>
						</div>

					</div>

					<div class="row">
						<div class="col-md-12">
							<nav aria-label="...">
							  <ul class="pager">
							    <li class="prev_tab"><a>Назад</a></li>
							    <li class="next_tab"><a>Далее</a></li>
							  </ul>
							</nav>
						</div>
					</div>
				</div>
		</div>
	</div>
</div>
<?php ActiveForm::end();?>

<?php 

$script = <<<JS
	//pager script
	$("body").on("click",".pager li",function(){
		var active_tab = $(".nav-tabs li.active");
		if($(this).hasClass("prev_tab")){
			if(active_tab.length){
				var prev = active_tab.prev();
				prev.length ? prev.find("a").trigger("click") : null;
			}
		}else if($(this).hasClass("next_tab")){
			if(active_tab.length){
				var next = active_tab.next();
				next.length ? next.find("a").trigger("click") : null;
			}
		}
	});




	//handler click on buttons for add form row

	var sendGetConsistRow = 0;
	$("#btnAddConsist,#btnAddWork").click(function(event){
		event.preventDefault();
		var action = $(this).attr("href");
		var table = $(this).parents("table");
		if(!table.length) return;
		var count = table.find("tbody tr").length;
		if(action && !sendGetConsistRow){
			$.ajax({
				url:action,
				type:"GET",
				data:{count:count},
				dataType:'json',
				beforeSend:function(){
					sendGetConsistRow = 1;
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
					sendGetConsistRow = 0;
				}
			});
		}
	});



	//handler click on remove row buttons
	$("body").on("click",'.btnRemoveRow',function(event){
		event.preventDefault();
		var tr = $(this).parents("tr");
		if(tr.length) tr.remove();
	});



	$("body").on("change",".spent_input",function(){
		var rest = $(this).parents("tr").find(".rest_input");
		var total = parseInt($(this).attr("max"));
		var value = parseInt($(this).val());
		rest.val(total - value);
	});

	$("body").on("keyup",".spent_input",function(){
		var rest = $(this).parents("tr").find(".rest_input");
		
		var total = parseInt($(this).attr("max"));
		var min = parseInt($(this).attr("min"));
		var value = parseInt($(this).val());

		if(min > value){
			$(this).val(min);
			value = min;
		}else if(total < value){
			$(this).val(total);
			value = total;
		}

		if(value || value === 0){
			var rest_value = parseInt(total - value);
			rest.val(rest_value);
		}else{
			rest.val("");
		}
		
	});

JS;


$this->registerJs($script);
?>