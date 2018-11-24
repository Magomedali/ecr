<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\models\User;
use common\widgets\autocomplete\AutoComplete;
use common\models\RaportWork;

$user = Yii::$app->user->identity;
$masters = User::find()->where(['is_master'=>true])->asArray()->all();

if(!$hasErrors){
	$BrigadeConsist = !isset($model->id) ? $user->brigadeConsist : $model->consist;
	$ActualBrigadeRemnants =!isset($model->id) ? $user->actualBrigadeRemnants : $model->materials;
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
	$boundary_name = isset($boundary->id) ? $boundary->name : "";

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

<?php $form = ActiveForm::begin(['id'=>'raportForm']);?>
<div class="row">
	<div class="col-md-12">
		<div class="row">
				<div class="col-md-12">
					<ul class="nav nav-tabs">	
					  <li class="first active"><a href="#base">Основное</a></li>
					  <li><a href="#consist">Состав бригады</a></li>
					  <li><a href="#works">Характеристики работ</a></li>
					  <li class='last'><a href="#remnants" >Остатки</a></li>
					</ul>
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
											
											<?php if(isset($model->id)){
												echo Html::hiddenInput('model_id',$model->id);
											}?>

											<?php echo $form->field($model,'brigade_guid')->hiddenInput(['value'=>$user->brigade_guid])->label(false);?>

											<?php echo $form->field($model,'created_at')->input("date",['value'=>date("Y-m-d"),'readonly'=>true,'class'=>'form-control input-sm']);?>

											<?php echo $form->field($model,'starttime')->input("time",['class'=>'form-control input-sm']);?>

											<?php echo $form->field($model,'endtime')->input("time",['class'=>'form-control input-sm']);?>
										</div>
										<div class="col-md-6 object_form">
											<div class="row">
												<div class="col-md-12">
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
													<?php echo Html::textInput("Raport[boundary_name]",$boundary_name,['class'=>'form-control input-sm input_boundary_name isRequired','readonly'=>true]);?>

													
													<?php echo $form->field($model,'boundary_guid')->hiddenInput(['class'=>'isRequired'])->label(false);?>
												</div>
												<div class="col-md-12">
													<div class="form-group <?php echo $inValidPassword ? 'has-error' :'';?>">
														<label>Пароль</label>
														<?php echo Html::input('password',"password",null,['class'=>"form-control input-sm input_password isRequired <?php echo $inValidPassword ? 'fieldHasError' :'';?>"]);?>
														<?php if($inValidPassword){?>
															<p class="help-block help-block-error">Неправильный пароль</p>
														<?php } ?>
													</div>
													
												</div>
											</div>
										</div>
									</div>
									<hr>
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class="col-md-6">
													<?php echo $form->field($model,'temperature_start')->input("number",['step'=>'0.01','class'=>'form-control input-sm']);?>
													<?php echo $form->field($model,'surface_temperature_start')->input("number",['step'=>'0.01','class'=>'form-control input-sm']);?>
													<?php echo $form->field($model,'airhumidity_start')->input("number",['step'=>'0.01','class'=>'form-control input-sm']);?>
												</div>
												<div class="col-md-6">
													<?php echo $form->field($model,'temperature_end')->input("number",['step'=>'0.01','class'=>'form-control input-sm']);
													?>
													<?php echo $form->field($model,'surface_temperature_end')->input("number",['step'=>'0.01','class'=>'form-control input-sm']);?>
													<?php echo $form->field($model,'airhumidity_end')->input("number",['step'=>'0.01','class'=>'form-control input-sm']);?>
												</div>
											</div>
										</div>
									</div>
									<hr>
									<div class="row">
										<div class="col-md-6">
											<?php echo $form->field($model,'comment')->textarea(['class'=>'form-control input-sm']);?>
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
														
														if($item['user_guid']){
															echo $item['user_name'];
															echo Html::hiddenInput("RaportConsist[$key][user_guid]",$item['user_guid']);
															echo Html::hiddenInput("RaportConsist[$key][user_name]",$item['user_name']);
														}else{
															echo AutoComplete::widget([
																'data'=>[],
																'apiUrl'=>Url::to(['/autocomplete/technics']),
																'inputValueName'=>"RaportConsist[$key][user_guid]",
																'inputValueName_Value'=>"",
																'inputKeyName'=>"RaportConsist[$key][user_name]",
																'inputKeyName_Value'=>"",
																'placeholder'=>'Укажите физ.лицо',
																'labelShow'=>false,
																'properties'=>[
																	['property'=>'ktu','commonElement'=>'tr','targetElement'=>'td.person_ktu span'],

																	['property'=>'ktu','commonElement'=>'tr','targetElement'=>'td.person_ktu input.hidden_user_ktu']
																]
															]);
														}
													?>
													</td>
													<td>
													<?php 
														if($item['technic_guid']){
															echo $item['technic_name'];
															echo Html::hiddenInput("RaportConsist[$key][technic_guid]",$item['technic_guid']);
															echo Html::hiddenInput("RaportConsist[$key][technic_name]",$item['technic_name']);
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
												<td>#</td>
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
													<td><?php echo $key+1; ?></td>
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
													<td>
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
													<td><?php echo Html::checkbox("RaportWork[$key][mechanized]",isset($item['mechanized']) ? $item['mechanized'] : null); ?></td>
													<td><?php echo Html::input("number","RaportWork[$key][length]",$item['length'],['class'=>'form-control isRequired input-sm','step'=>"0.01"]); ?></td>
													<td><?php echo Html::input("number","RaportWork[$key][count]",$item['count'],['class'=>'form-control isRequired input-sm','step'=>"0.01"]); ?></td>
													<td><?php echo Html::textInput("RaportWork[$key][squaremeter]",$item['squaremeter'],['class'=>'form-control input-sm','readonly'=>1]); ?></td>
													<td><?php echo html::a('-',null,['class'=>'btn btn-sm btn-danger btnRemoveRow']);?></td>
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
														echo Html::input("number","RaportMaterial[$key][spent]",$item['spent'],['class'=>'form-control input-sm spent_input isRequired','min'=>0,'max'=>$item['was']]);
													?>
													</td>
													<td>
														<?php 
															echo Html::textInput("RaportMaterial[$key][rest]",$item['rest'],['class'=>'form-control input-sm rest_input','readonly'=>1]);
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
							    <li class="prev_tab" style="display: none;"><a>Назад</a></li>
							    <li class="next_tab"><a>Далее</a></li>
							    <li class="submit" style="display: none;">
									<?php echo Html::submitButton("Сохранить",['class'=>'btn btn-primary','id'=>'btnRaportFormSubmit']);?>
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

		$("ul.nav.nav-tabs li a").removeClass("hasError");

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

						var tab = $("ul.nav.nav-tabs").find("a[href=\"#"+tabContentId+"\"]");
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
		if(!validateRaportForm()){
			event.preventDefault();
		}
	});




	//pager script
	$("body").on("click",".pager li",function(){
		if($(this).hasClass('submit')) return;
		
		var tabs = $(".tab-content");
		
		var active_tab = $(".nav-tabs li.active");
		var target_tab = null;
		if(!active_tab.length) return;

		// console.log()

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
		active_tab.removeClass("active");
		target_tab.addClass("active");
		tabs.find("div.tab-pane"+target_tab.find("a").attr("href")).addClass("active");
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

		var table = $(this).parents("table");
		if(table.find("tbody tr").length > 1){
			var tr = $(this).parents("tr");
			if(tr.length) tr.remove();
		}
		
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