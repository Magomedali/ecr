<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\models\User;
use common\widgets\autocomplete\AutoComplete;
use common\models\RaportWork;
use common\dictionaries\RaportStatuses;

$user = Yii::$app->user->identity;
$masters = User::find()->where(['is_master'=>true])->asArray()->all();

if(!$hasErrors){
	$BrigadeConsist =  $model->consist;
	$ActualBrigadeRemnants =  $model->materials;
	

	if(isset($model->id)){
		if($model->isWrongMaterials($ActualBrigadeRemnants)){
			Yii::$app->session->setFlash("warning","В остатках недостаточное количество материалов!");
		}
	}

	$RaportWorks =isset($model->id) ? $model->works : [[
		'work_guid'=>null,
		'work_name'=>null,
		'line_guid'=>null,
		'line_name'=>null,
		'mechanized'=>null,
		'length'=>null,
		'count'=>null,
		'squaremeter'=>null
	]];
}else{
	$BrigadeConsist = $errorsRaportConsist;
	$ActualBrigadeRemnants = $errorsRaportMaterials;
	$RaportWorks = $errorsRaportWorks;
}



if(isset($model->id)){
	$object = $model->object_guid ? $model->object : null;
	$object_name = isset($object->id) ? $object->name : "";

	$boundary = isset($object->id) && $object->boundary_guid ? $object->boundary : null;
	$boundary_name = isset($boundary->id) ? $boundary->name : null;

	$project = isset($model->project_guid) ? $model->project : null;
	$project_name = isset($project->id) ? $project->name : null;

	$master = isset($model->master_guid) ? $model->master : null;
	$master_name = isset($master->id) ? $master->name : null;
}else{
	$boundary = $object = $project = null;
	if($hasErrors){
		$boundary_name = $errorsRaport['boundary_name'];
		$project_name = $errorsRaport['project_name'];
		$object_name = $errorsRaport['object_name'];
		$master_name = $errorsRaport['master_name'];;
	}else{
		$boundary_name = $project_name = $object_name = $master_name = "";
	}
}


$this->title = "Форма рапорта";

?>

<?php $form = ActiveForm::begin(['id'=>'raportForm','options'=>['enctype'=>'multipart/form-data','autocomplete'=>'off']]);?>



<div class="row">
	<div class="col-md-12">
		<div class="row">
				<div class="col-md-12">
					<div class="row pipeline">	
					  <div class="col-md-3 first active"><a href="#base"><span>Основное</span></a></div>
					  <div class="col-md-3 red"><a href="#consist"><span>Состав бригады</span></a></div>
					  <div class="col-md-3 red"><a href="#works"><span>Характеристики работ</span></a></div>
					  <div class='col-md-3 red last'><a href="#remnants" ><span>Остатки</span></a></div>
					</div>
					<div class="tab-content">

						<!-- Основное -->
						<div id="base" class="tab-pane fade in active">
							<h3>Основное</h3>
							<div class="row">
								<div class="col-md-12">
									<div class="row">
										<div class="col-md-6">
											<?php 
												echo AutoComplete::widget([
													'data'=>ArrayHelper::map($masters,'guid','name'),
													'apiUrl'=>Url::to(['/autocomplete/masters']),
													'inputValueName'=>'Raport[master_guid]',
													'inputValueName_Value'=>$model->master_guid,
													'inputKeyName'=>'Raport[master_name]',
													'inputKeyName_Value'=>$master_name,
													'placeholder'=>'Укажите мастера',
													'label'=>'Мастер'
												]);
											?>
											
											<?php if(isset($model->id)){ echo Html::hiddenInput('model_id',$model->id); }?>

											<?php echo $form->field($model,'brigade_guid')->hiddenInput()->label(false); ?>
											
											<?php echo $form->field($model,'user_guid')->hiddenInput()->label(false); ?>

											<?php echo $form->field($model,'created_at')->input("datetime-local",['value'=>isset($model->id) ? date("Y-m-d\TH:i:s",strtotime($model->created_at)) : date("Y-m-d\TH:i:s",time()),'readonly'=>true,'class'=>'form-control input-sm']); ?>

											<?php echo $form->field($model,'starttime')->input("time",['class'=>'form-control input-sm isRequired']); ?>

											<?php echo $form->field($model,'endtime')->input("time",['class'=>'form-control input-sm isRequired']);?>
										</div>
										<div class="col-md-6 object_form">
											<div class="row">
												<div class="col-md-12 object_autocomplete">
													<?php
                                                        echo AutoComplete::widget([
															'data'=>[],
															'apiUrl'=>Url::to(['/autocomplete/objects']),
															'inputValueName'=>"Raport[object_guid]",
															'inputValueName_Value'=>$model->object_guid,
															'inputKeyName'=>'Raport[object_name]',
															'inputKeyName_Value'=>$object_name,
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
															'inputValueName_Value'=>$model->project_guid,
															'inputKeyName'=>'Raport[project_name]',
															'inputKeyName_Value'=>$project_name,
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
													<?php echo Html::textInput("Raport[boundary_name]",$boundary_name,['class'=>'form-control input-sm input_boundary_name','readonly'=>true]);?>

													
													<?php echo $form->field($model,'boundary_guid')->hiddenInput()->label(false);?>
												</div>
												<div class="col-md-12">
													<?php
														echo $form->field($model,'status')->dropDownList(RaportStatuses::getLabels());
													?>
												</div>
											</div>
										</div>
									</div>
									<hr>
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class="col-md-6">
													<?php echo $form->field($model,'temperature_start')->input("number",['step'=>'0.01','class'=>'form-control input-sm isRequired','autocomplete'=>'off']);?>
													<?php echo $form->field($model,'surface_temperature_start')->input("number",['step'=>'0.01','class'=>'form-control input-sm isRequired','autocomplete'=>'off']);?>
													<?php echo $form->field($model,'airhumidity_start')->input("number",['step'=>'0.01','class'=>'form-control input-sm isRequired','autocomplete'=>'off']);?>
												</div>
												<div class="col-md-6">
													<?php echo $form->field($model,'temperature_end')->input("number",['step'=>'0.01','class'=>'form-control input-sm isRequired','autocomplete'=>'off']);
													?>
													<?php echo $form->field($model,'surface_temperature_end')->input("number",['step'=>'0.01','class'=>'form-control input-sm isRequired','autocomplete'=>'off']);?>
													<?php echo $form->field($model,'airhumidity_end')->input("number",['step'=>'0.01','class'=>'form-control input-sm isRequired','autocomplete'=>'off']);?>
												</div>
											</div>
										</div>
									</div>
									<hr>
									<div class="row">
										<div class="col-md-6">
											<?php echo $form->field($model,'comment')->textarea(['class'=>'form-control input-sm','autocomplete'=>'off']);?>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="form-label">Прикрепить файлы:</label>
												<?php echo Html::fileInput("files[]",null,['multiple'=>true]);?>
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
													<td>
													<?php 
														
														if($item['user_guid']){
															echo $item['user_name'];
															echo Html::hiddenInput("RaportConsist[$key][user_guid]",$item['user_guid']);
															echo Html::hiddenInput("RaportConsist[$key][user_name]",$item['user_name']);
														}else{
															echo AutoComplete::widget([
																'data'=>[],
																'apiUrl'=>Url::to(['/autocomplete/users']),
																'inputValueName'=>"RaportConsist[$key][user_guid]",
																'inputValueName_Value'=>"",
																'inputKeyName'=>"RaportConsist[$key][user_name]",
																'inputKeyName_Value'=>"",
																'placeholder'=>'Укажите физ.лицо',
																'labelShow'=>false,
																'properties'=>[
																	['property'=>'ktu','commonElement'=>'tr','targetElement'=>'td.person_ktu span'],
																	['property'=>'ktu','commonElement'=>'tr','targetElement'=>'td.person_ktu input.hidden_user_ktu'],

																	//['property'=>'technic_guid','commonElement'=>'tr','targetElement'=>'td.td_technic input.autocomplete_input_value'],
																	//['property'=>'technic_name','commonElement'=>'tr','targetElement'=>'td.td_technic input.autocomplete_input_key']
																]
															]);
														}
													?>
													</td>
													<td class="td_technic">
													<?php 
														if($item['technic_guid']){
															//echo $item['technic_name'];
															//echo Html::hiddenInput("RaportConsist[$key][technic_guid]",$item['technic_guid']);
															//echo Html::hiddenInput("RaportConsist[$key][technic_name]",$item['technic_name']);
															echo AutoComplete::widget([
																'data'=>[],
																'apiUrl'=>Url::to(['/autocomplete/technics']),
																'inputValueName'=>"RaportConsist[$key][technic_guid]",
																'inputValueName_Value'=>$item['technic_guid'],
																'inputKeyName'=>"RaportConsist[$key][technic_name]",
																'inputKeyName_Value'=>$item['technic_name'],
																'placeholder'=>'Укажите технику',
																'labelShow'=>false
															]);
														}else{
															echo AutoComplete::widget([
																'data'=>[],
																'apiUrl'=>Url::to(['/autocomplete/technics']),
																'inputValueName'=>"RaportConsist[$key][technic_guid]",
																'inputValueName_Value'=>"",
																'inputKeyName'=>"RaportConsist[$key][technic_name]",
																'inputKeyName_Value'=>"",
																'placeholder'=>'Укажите технику',
																'labelShow'=>false
															]);
														}
														
													?>
													</td>
													<td class="person_ktu">
														<span><?php echo $item['user_ktu'];?></span>
														<?php echo Html::hiddenInput("RaportConsist[{$key}][user_ktu]",$item['user_ktu'],['class'=>'hidden_user_ktu'])?>
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
						</div>


						<!-- Характеристики работ -->
						<div id="works" class="tab-pane fade in">
							<h3>Характеристики работ</h3>
							<div class="row">
								<div class="col-md-12">
									<table id="tableWorks" class="table table-bordered table-hovered table-collapsed">
										<thead>
											<tr>
												<td>Вид работы</td>
												<td>Линия</td>
												<td>Механизировання</td>
												<td>П.М./Шт</td>
												<td>Количество</td>
												<td>кв. м</td>
												<td><?php echo html::a('+',['raport/get-row-work'],['class'=>'btn btn-sm btn-primary','id'=>'btnAddWork'])?></td>
											</tr>
										</thead>
										<tbody>
											<?php if(is_array($RaportWorks)){?>
												<?php foreach ($RaportWorks as $key => $item) {?>
												<tr>
													<td>
													<?php 
														echo AutoComplete::widget([
															'data'=>[],
															'apiUrl'=>Url::to(['/autocomplete/works']),
															'inputValueName'=>"RaportWork[$key][work_guid]",
															'inputValueName_Value'=>$item['work_guid'],
															'inputKeyName'=>"RaportWork[$key][work_name]",
															'inputKeyName_Value'=>$item['work_name'],
															'placeholder'=>'Укажите вид работы',
															'labelShow'=>false
														]);
													?>
													</td>
													<td class="td_line_guid">
													<?php 
														echo AutoComplete::widget([
															'data'=>[],
															'apiUrl'=>Url::to(['/autocomplete/lines']),
															'inputValueName'=>"RaportWork[$key][line_guid]",
															'inputValueName_Value'=>$item['line_guid'],
															'inputKeyName'=>"RaportWork[$key][line_name]",
															'inputKeyName_Value'=>$item['line_name'],
															'placeholder'=>'Укажите линию',
															'labelShow'=>false
														]);
													?>	
													</td>
													<td>
														<?php echo Html::checkbox("RaportWork[$key][mechanized]",isset($item['mechanized']) ? $item['mechanized'] : null); ?>
													</td>
													<td class="td_length">
														<?php echo Html::input("number","RaportWork[$key][length]",$item['length'],['class'=>'form-control isRequired input-sm','step'=>"0.01",'autocomplete'=>'off']); ?>
													</td>
													<td  class="td_count">
														<?php echo Html::input("number","RaportWork[$key][count]",$item['count'],['class'=>'form-control isRequired input-sm','step'=>"0.01",'autocomplete'=>'off']); ?>
													</td>
													<td class="td_squaremeter">
														<?php echo Html::textInput("RaportWork[$key][squaremeter]",$item['squaremeter'],['class'=>'form-control input-sm','readonly'=>1]); ?>
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



						<!-- Остатки -->
						<div id="remnants" class="tab-pane fade in">
							<h3>Остатки</h3>
							<div class="row">
								<div class="col-md-12">
									<table id="tableRemnants" class="table table-bordered table-hovered table-collapsed">
										<thead>
											<tr>
												<td>Номенклатура</td>
												<td>Начальный остаток</td>
												<td>Израсходовано</td>
												<td>Конечный остаток</td>
											</tr>
										</thead>
										<tbody>
											<?php if(is_array($ActualBrigadeRemnants)){
												$key = 0;
												foreach ($ActualBrigadeRemnants as $index => $item) {

											?>
												<tr>
													<td>
													<?php 
														echo $item['nomenclature_name'];
														echo Html::hiddenInput("RaportMaterial[$key][nomenclature_name]",$item['nomenclature_name']);
														echo Html::hiddenInput("RaportMaterial[$key][nomenclature_guid]",$item['nomenclature_guid']);
													?>
													</td>
													<td>
													<?php 
														echo Html::textInput("RaportMaterial[$key][was]",$item['was'],['class'=>'form-control input-sm was_input ','readonly'=>1]);
													?>
													</td>
													<td>
													<?php 
														echo Html::input("number","RaportMaterial[$key][spent]",$item['spent'] ? $item['spent'] : null,['class'=>'form-control input-sm spent_input','min'=>0,'step'=>"0.01",'max'=>$item['was'],'autocomplete'=>'off']);
													?>
													</td>
													<td>
														<?php 
															echo Html::textInput("RaportMaterial[$key][rest]",$item['rest'],['class'=>'form-control input-sm rest_input','readonly'=>1]);
														?>
													</td
												</tr>
											<?php
												$key++;
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
							    <li class="prev_tab" style="display: none;"><a>Назад</a></li>
							    <li class="next_tab"><a>Далее</a></li>
							    <li class="submit" style="display: none;">
									<?php echo Html::submitButton("Сохранить",['class'=>'btn btn-primary','id'=>'btnRaportFormSubmitPassword']);?>
							    </li>
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

	var requiredFields = [
		"input.autocomplete_required",
		"input.isRequired"
	];

	

	var validateRaportForm = function(){

		var hasError = false;

		$("div.pipeline div a").removeClass("hasError");

		if(requiredFields.length){
			$.each(requiredFields,function(i,field){
				var fieldsForms = $(field);

				if(!fieldsForms.length) return;

				fieldsForms.each(function(){
					var fieldForm = $(this);
					
					if(!fieldForm.val()){
						hasError = true;

						fieldForm.addClass("fieldHasError");
						var tabContent = fieldForm.parents("div.tab-pane");
						if(!tabContent.length) return;

						var tabContentId = tabContent.attr("id");
								
						if(!tabContentId) return;

						var tab = $("div.pipeline div").find("a[href=\"#"+tabContentId+"\"]");
						if(!tab.length) return;

						tab.addClass("hasError");

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
	$("form#raportForm").submit(function(event){
	    $("#btnRaportFormSubmitPassword").prop("disabled",true);
		if(!validateRaportForm()){
			event.preventDefault();
	        $("#btnRaportFormSubmitPassword").prop("disabled",false);
		}
	});




	//pager script
	$("body").on("click",".pager li",function(){
		if($(this).hasClass('submit')) return;
		
		var tabs = $(".tab-content");
		var pipeline = $("div.pipeline div");
		var active_tab = $("div.pipeline div.active");
		var target_tab = null;
		if(!active_tab.length) return;


		if($(this).hasClass("prev_tab")){
			var target_tab = active_tab.prev();
			if(!target_tab.length) return;
					
			if(target_tab.hasClass("first")){
				$(this).hide();
			}

			$(".pager li.next_tab").show();
			$(".pager li.submit").hide();
			$(".pager li.submit").hide();

		}else if($(this).hasClass("next_tab")){
			
			var target_tab = active_tab.next();
			if(!target_tab.length) return;

			if(!validateRaportForm() && active_tab.find("a").hasClass("hasError")){
				return;
			}

			if(target_tab.hasClass("last")){
				$(this).hide();
				$(".pager li.submit").show();
			}else{
				$(".pager li.submit").hide();
			}

			$(".pager li.prev_tab").show();
		}

		tabs.find("div.tab-pane").removeClass("active");
		pipeline.removeClass("green");
		pipeline.removeClass("red");
		active_tab.removeClass("active");
		target_tab.addClass("active");
		
		target_tab.nextAll().addClass("red");
		target_tab.prevAll().addClass("green");

		tabs.find("div.tab-pane"+target_tab.find("a").attr("href")).addClass("active");
	});



	$("div.pipeline > div").hover(function(){
		$(this).addClass("hover");
	},function(){
		$(this).removeClass("hover");
	});



	$("div.pipeline > div").click(function(){
		var i = $(this).index();
		var i_a = $("div.pipeline > div.active").index();

		var count = Math.abs(i-i_a);
		for(var k = 0; k < count; k++){
			if(i > i_a){
				$(".pager li.next_tab").trigger("click");
			}else if(i_a > i){
				$(".pager li.prev_tab").trigger("click");
			}
		}

	})
	




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

		var table = $(this).parents("table");
		if(table.find("tbody tr").length > 1){
			var tr = $(this).parents("tr");
			if(tr.length) tr.remove();
		}
		
	});



	$("body").on("change",".spent_input",function(){
		var rest = $(this).parents("tr").find(".rest_input");
		var total = parseFloat($(this).attr("max"));
		var value = parseFloat($(this).val());
		rest.val(total - value);
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
			rest.val(rest_value);
		}else{
			rest.val("");
		}
		
	});




	//Function calcsquare
	var calcsquare = function(tr){
		if(!tr.length) return;

		var line_guid = tr.find("td.td_line_guid input.autocomplete_input_value").val();
		var count = tr.find("td.td_count input").val();
		var length = tr.find("td.td_length input").val();
		
		if(!line_guid || !length) return;

		$.ajax({
			url:"index.php?r=autocomplete/calcsquare",
			data:{
				line_guid:line_guid,
				count:count,
				length:length,
			},
			type:"GET",
			dataType:"json",
			beforeSend:function(){

			},
			success:function(json){
				if(json.hasOwnProperty("result") && json.result){
					tr.find("td.td_squaremeter input").val(json.result);
				}
				
			},
			error:function(e){
				console.log(e);
			},
			complete:function(){

			},
		})
	}

	//Расчет кв м
	$("body").on("change",".td_length input,.td_count input",function(){
		var tr = $(this).parents("tr");
		calcsquare(tr);
	});
	
	//Расчет кв м
	$("body").on("keyup",".td_length input,.td_count input",function(){
		var tr = $(this).parents("tr");
		calcsquare(tr);
	});

	//Расчет кв м
	$("body").on("click",".td_line_guid .autocomplete_items li",function(){
		var tr = $(this).parents("tr");
		calcsquare(tr);
	});

	//Открываем список проектов при выборе объекта
	$("body").on("click",".object_autocomplete ul.autocomplete_items li",function(){
		var project_at = $(".autocomplete__widget_block input[name='Raport[project_name]']");
		if(project_at.length){
			//очищаем ранее выбранное значение
			$("input[name='Raport[project_name]']").val(null);
			$("input[name='Raport[project_guid]']").val(null);
			project_at.focus();
		}
	})

JS;


$this->registerJs($script);
?>