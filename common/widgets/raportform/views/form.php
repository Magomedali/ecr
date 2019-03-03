<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use common\widgets\autocomplete\AutoComplete;

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
																['property'=>'boundary_name','commonElement'=>'div.object_form','targetElement'=>'input.input_boundary_name'],

																['property'=>'master_guid','commonElement'=>'div#base','targetElement'=>'div#master_autocomplete input.autocomplete_input_value'],
																['property'=>'master_name','commonElement'=>'div#base','targetElement'=>'div#master_autocomplete input.autocomplete_input_key']
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
											</div>
										</div>

										<div class="col-md-6">
											<div class="row">
												<div class="col-md-12">
													<?php if(!$disableMaster || !$model->master_guid){ ?>
													<div id="master_autocomplete">
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
													</div>
													<?php }else{
															echo Html::hiddenInput("Raport[master_guid]",$model->master_guid);
														}
													?>
													
													


													<?php echo $form->field($model,'created_at')->input("datetime-local",['value'=>isset($model->id) ? date("Y-m-d\TH:i:s",strtotime($model->created_at)) : date("Y-m-d\TH:i:s",time()),'readonly'=>true,'class'=>'form-control input-sm']); ?>

													<?php echo $form->field($model,'starttime')->input("time",['class'=>'form-control input-sm isRequired']); ?>

													<?php echo $form->field($model,'endtime')->input("time",['class'=>'form-control input-sm isRequired']);?>

													<?php if(isset($model->id)){ echo Html::hiddenInput('model_id',$model->id); }?>

													<?php 
														if($forManager){
															echo $form->field($model,'brigade_guid')->hiddenInput()->label(false);
															echo $form->field($model,'user_guid')->hiddenInput()->label(false);
														} 
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
										<?php if($updateStatus){ ?>
										<div class="col-md-3">
											<?php
												echo $form->field($model,'status')->dropDownList($statuses);
											?>
										</div>
										<?php } ?>

										<?php
											if($requiredFile){
										?>
										<div class="col-md-3">
											<div class="form-group">
												<label class="form-label">Прикрепить файлы:</label>

												<?php 
													$fileIsRequired = !isset($model->id) ? "isRequired" : "" ;
												echo Html::fileInput("files[]",null,['multiple'=>true,'class'=>$fileIsRequired]);?>
											</div>
										</div>
										<?php 
											} 
										?>
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
												<td><?php echo html::a('+',$urlLoadRaportConsistRow,['class'=>'btn btn-sm btn-primary','id'=>'btnAddConsist'])?></td>
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
																	['property'=>'exists_technic','commonElement'=>'tr','targetElement'=>'td.td_technic input.mock_object'],
																],
																'generateSearchFiltersCallback'=>"function(){
																	
																	var users = $('#tableConsist').find('input[name$=\'[user_guid]\'][name^=\'RaportConsist\']');

																	if(users.length){
																		var data = [];
																		users.each(function(){
																			data.push($(this).val());
																		});

																		return {
																			users_extends:data
																		}

																	}else{
																		return {};
																	}
																}",
																'onSelectCallback'=>"function(item){
																	if(!item.length) return;
																	var exists_technic = item.attr('data-exists_technic');
																	var commonEl = item.parents('tr');
																	var InputElements = commonEl.find('td.td_technic input.autocomplete_input_key,td.td_technic input.autocomplete_input_value');

																	if(exists_technic !== 'true'){
																		InputElements.removeClass('autocomplete_required');
																		InputElements.removeClass('fieldHasError');
																		InputElements.addClass('fieldIsSuccess');
																		InputElements.val(null);
																	}else{
																		InputElements.addClass('autocomplete_required');
																	}											
																}"
															]);
														}
													?>
													</td>
													<td class="td_technic">
													<?php 
														if($item['technic_guid']){
															echo AutoComplete::widget([
																'data'=>[],
																'apiUrl'=>Url::to(['/autocomplete/technics']),
																'inputValueName'=>"RaportConsist[$key][technic_guid]",
																'inputValueName_Value'=>$item['technic_guid'],
																'inputKeyName'=>"RaportConsist[$key][technic_name]",
																'inputKeyName_Value'=>$item['technic_name'],
																'placeholder'=>'Укажите технику',
																'labelShow'=>false,
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
																'labelShow'=>false,
																'required'=>false
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
												<td><?php echo html::a('+',$urlLoadRaportWorkRow,['class'=>'btn btn-sm btn-primary','id'=>'btnAddWork'])?></td>
											</tr>
										</thead>
										<tbody>
											<?php $common_square = null;

											if(is_array($RaportWorks)){?>
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
															'labelShow'=>false,
															'properties'=>[
																['property'=>'work_nomenclatures','commonElement'=>'td','targetElement'=>'.work_assigned_nomencaltures'],
															],
															'generateSearchFiltersCallback'=>"function(){
																return {
																	extends:{
																		is_regulatory:0
																	}
																}
															}"
														]);
													?>
													<?php
														echo Html::hiddenInput("RaportWork[$key][work_nomenclatures]",isset($item['work_nomenclatures']) ? $item['work_nomenclatures'] : null,['class'=>'work_assigned_nomencaltures']);
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
															'labelShow'=>false,
															'properties'=>[
																['property'=>'hint_length','commonElement'=>'tr','targetElement'=>'td.td_length span.hint_length,td.td_length input[type=hidden].hint_length'],
																['property'=>'hint_count','commonElement'=>'tr','targetElement'=>'td.td_count span.hint_count,td.td_count input[type=hidden].hint_count'],
																['property'=>'is_countable','commonElement'=>'tr','targetElement'=>'td.td_count input[type=hidden].is_countable'],
															],
															'onSelectCallback'=>"function(item){
																if(!item.length) return;
																var is_countable = parseInt(item.attr('data-is_countable'));
																var commonEl = item.parents('tr');
																var InputElement = commonEl.find('td.td_count input[type=number]');
																InputElement.attr('readonly',!is_countable);
																

																if(!is_countable){
																	commonEl.find('td.td_count input[type=hidden].hint_count').val(null);
																	commonEl.find('td.td_count span.hint_count').html(null);
																	InputElement.removeClass('isRequired');
																	InputElement.removeClass('fieldHasError');
																	InputElement.val(null);
																}else{
																	InputElement.addClass('isRequired');
																}
																
															}"
														]);
													?>	
													</td>
													<td>
														<?php echo Html::checkbox("RaportWork[$key][mechanized]",isset($item['mechanized']) ? $item['mechanized'] : null); ?>
													</td>
													<td class="td_length">
														<?php echo Html::input("number","RaportWork[$key][length]",$item['length'],['class'=>'form-control isRequired input-sm','step'=>"0.01",'autocomplete'=>'off']); ?>
														<?php echo Html::hiddenInput("RaportWork[$key][hint_length]",isset($item['hint_length']) ? $item['hint_length'] : "",['class'=>'hint_length'])?>
														<span class="hint_field hint_length"><?php echo isset($item['hint_length']) ? $item['hint_length'] : ""?></span>
													</td>
													<td  class="td_count">
														<?php 
														$countisRequired = isset($item['is_countable']) && boolval($item['is_countable']) ? "isRequired" : null;

														echo Html::input("number","RaportWork[$key][count]",
															$countisRequired ? $item['count'] : null,
															[
															'class'=>'form-control input-sm '.$countisRequired,
															'step'=>"0.01",
															'autocomplete'=>'off',
															'readonly'=>!boolval($countisRequired)
															]
														); 
														?>
														<?php echo Html::hiddenInput("RaportWork[$key][hint_count]",isset($item['hint_count']) ? $item['hint_count'] : "",['class'=>'hint_count'])?>

														<?php echo Html::hiddenInput("RaportWork[$key][is_countable]",isset($item['is_countable']) ? $item['is_countable'] : "",['class'=>'is_countable'])?>
														<span class="hint_field hint_count"><?php echo isset($item['hint_count']) ? $item['hint_count'] : ""?></span>
													</td>
													<td class="td_squaremeter">
														<?php 
															echo Html::textInput("RaportWork[$key][squaremeter]",$item['squaremeter'],['class'=>'form-control input-sm','readonly'=>1]);
															$common_square +=$item['squaremeter'];
														?>
													</td>
													<td>
														<?php echo html::a('-',null,['class'=>'btn btn-sm btn-danger btnRemoveRow']);?>
													</td>
												</tr>
												<?php } ?>
											<?php } ?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="5">Итого</th>
												<th><span class='common_square'>
													<?php echo $common_square;?>
												</span></th>
												<th></th>
											</tr>
										</tfoot>
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
													$assigned = isset($item['assigned']) && (int)($item['assigned']);
											?>
												<tr data-assigned="<?php echo $assigned ? 1 : 0;?>">
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
													<td class="tableRemnant_spent">
														<?php 
															echo Html::input("number","RaportMaterial[$key][spent]",$item['spent'] ? $item['spent'] : null,['class'=>'form-control input-sm spent_input','min'=>0,'step'=>"0.001",'max'=>$item['was'],'autocomplete'=>'off','readonly'=>false,'data-value'=>$item['spent'] ? $item['spent'] : null]); //boolval($assigned)
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
							<div id="projectStandarts" class="row">
								<div class="col-md-12">
									<h3>Норматив:</h3>
									<table id="tableProjectStandarts" class="table table-bordered table-hovered table-collapsed">
										<thead>
											<tr>
												<th>Вид работы</th>
												<th>Общий объем m2</th>
												<th>Норматив</th>
												<th>Средний расход</th>
												<th>Отклонение</th>
											</tr>
										</thead>
										<tbody>
											
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
							    
							    <?php if(!$enableGuardPassword){ ?>
								    <li class="submit" style="display: none;">
										<?php echo Html::submitButton("Сохранить",['class'=>'btn btn-primary','id'=>'btnRaportFormSubmitPassword']);?>
								    </li>
							    <?php }else{ ?> 
								    <li class="submit" style="display: none;">
										<?php echo Html::a("Сохранить",null,['id'=>'btnRaportFormSubmit']);?>
								    </li>
								<?php } ?>
							  </ul>
							</nav>
						</div>
					</div>
				</div>
		</div>
	</div>
</div>
<?php

	if($enableGuardPassword){
		Modal::begin([
				'header'=>"<h4>Введите пароль от учетной записи</h4>",
				'id'=>'modalPassword'
			]);

			$invalidClass =  $inValidPassword ? 'fieldHasError' :'';
		?>
		<div id="modalContent">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group <?php echo $inValidPassword ? 'has-error' :'';?>">
						<label>Пароль</label>
						<?php echo Html::input('password',"password",null,['class'=>"form-control input-sm input_password {$invalidClass}",'required'=>true,'id'=>'input_password','autocomplete'=>'off']);?>
						<?php if($inValidPassword){?>
							<p class="help-block help-block-error">Неправильный пароль</p>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<?php echo Html::submitButton("Сохранить",['class'=>'btn btn-primary','id'=>'btnRaportFormSubmitPassword']);?>
				</div>
			</div>
		</div>
<?php	
			Modal::end();
		} 
?>


<?php ActiveForm::end();?>

<?php 

$script = <<<JS

	var requiredFields = [
		"input.autocomplete_required",
		"input.isRequired"
	];

	var enableGuardPassword = parseInt({$enableGuardPassword});
	
	var validatePassword = function(){

		var i = $("#input_password");
		if(!i.length) return false;

		if(i.val().length < 6) return false;

		return true;
	}

	var behaviorWhenSuccess = function(input){
			if(input.hasClass('autocomplete_required')){
				var val_input = input.siblings("input.autocomplete_input_value");
				if(val_input.val()){
					val_input.removeClass("fieldHasError");
					input.removeClass("fieldHasError");
					input.addClass("fieldIsSuccess");
				}
			}else{
				
				input.addClass("fieldIsSuccess");
			};
	};

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
						behaviorWhenSuccess(fieldForm);
					}
				})

			});
		}

		return !hasError;
	}

	//Запускаем проверку формы
	validateRaportForm();

	//Если требуется пароль
	if(enableGuardPassword){
		$("#btnRaportFormSubmit").click(function(event){
			event.preventDefault();
			if(validateRaportForm()){
				$("#modalPassword").modal('show');
			}
		});
	}

	


	//form submit
	$("form#raportForm").submit(function(event){
	    $("#btnRaportFormSubmitPassword").prop("disabled",true);
		
		if(!validateRaportForm() || (enableGuardPassword && !validatePassword())){
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
		var thisBtn = $(this);
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
					thisBtn.prop("disabled",true);
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
					thisBtn.prop("disabled",false);
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

		if(table.attr("id") == "tableWorks"){
			updateRemnantsTable();//блокировка и разблокировка номенклатур
			drawProjectStandart();//отрисовка таблицы норматива
			calcAVGStandard();//рассчет норматива
		};
	});



	$("body").on("change",".spent_input",function(){
		var rest = $(this).parents("tr").find(".rest_input");
		var total = parseFloat($(this).attr("max"));
		var value = parseFloat($(this).val());
		var r = parseFloat(total - value);
		r = r > 0 || r < 0 ? r : 0; 
    	rest.val(r.toFixed(3));

    	calcAVGStandard();//рассчет норматива
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

		calcAVGStandard();//рассчет норматива
	});


	var calcCommonSquare = function(){
		var cc = parseFloat(0);

		var trs = $("#tableWorks tbody tr td.td_squaremeter input");
		if(!trs.length) return;
		trs.each(function(){
			cc += parseFloat($(this).val());
		});

		$("#tableWorks tfoot span.common_square").text(parseFloat(cc).toFixed(3));
	};

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
					calcCommonSquare();//рассчет общей площади
					calcAVGStandard();//рассчет норматива
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


	//рассчет норматива при выборе вида работы
	$("body").on("click","input[name^=\'RaportWork\'][name$=\'[work_guid]\'] ~ div.autocomplete_data .autocomplete_items li",function(){
		calcAVGStandard();
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
	});

	$("body").on("focus","td.td_length input[type=number],td.td_count input[type=number]",function(){
		var hint = $(this).siblings(".hint_field");
		hint.show();
	});


	$("body").on("focusout","td.td_length input[type=number],td.td_count input[type=number]",function(){
		var hint = $(this).siblings(".hint_field");
		hint.hide();
	});

	var projectStandarts = {
		data:[]
	}

	var disableRemnantsField = function(){
		var remnants = $("#tableRemnants tr");
		if(!remnants.length) return;

		remnants.each(function(){
			var assigned = $(this).data("assigned");
			if(assigned == 'undefined') return;
			var rem = $(this).find("td.tableRemnant_spent input");;
			if(!rem.length) return;
			assigned = parseInt(assigned) ? true : false;

			if(assigned){
				rem.prop("readonly",true);
				if(rem.val()){
					rem.attr("data-value",rem.val());
				}
				rem.val(null);
			}
		});
	}

	var updateRemnantsTable = function(){
		// console.log("Блокировка/Разблокировка израсходовано");
		var works = $("#tableWorks tr");
		
		disableRemnantsField();

		if(!works.length){
			return;
		}

		works.each(function(){
			var work = $(this);
			var nomens = work.find("input.work_assigned_nomencaltures").val();
			if(!nomens) return;
			var arr_nomens = nomens.split("|");
			
			for(var i = 0; i < arr_nomens.length; i++){
				var rem = $("#tableRemnants input[name$=\'[nomenclature_guid]\'][value=\'"+arr_nomens[i]+"\']");
				if(!rem.length) continue;
				
				var spent = rem.parents("tr").find(".tableRemnant_spent input");
				if(!spent.length) continue;
				spent.prop("readonly",false);
				var old_val = spent.attr("data-value");
				spent.val(old_val != 'undefined' ? old_val : null);
			}
			
		});
	};

	
	var loadStandarsUrl = '{$loadStandarsUrl}';

	var loadStandars = function(){
		var guid = $("input[name=\'Raport[project_guid]\']");
		if(guid.length && guid.val()){
			$.ajax({
				url:loadStandarsUrl,
				data:{
					guid:guid.val()
				},
				type:"GET",
				dataType:"json",
				beforeSend:function(){

				},
				success:function(resp){
					projectStandarts.data = resp;
					drawProjectStandart();
					calcAVGStandard();
				},
				error:function(msg){
					console.log(msg);
				},
				complete:function(){

				}
			});
		}
	}
	
	updateRemnantsTable();
	loadStandars();

	var drawProjectStandart = function(){
		// console.log("Рисуем таблицу нормативов для выбранного проекта");

		$("#tableProjectStandarts tbody").html("");
		
		if(!projectStandarts.data.length){
			return;
		}
		// console.log(projectStandarts.data);
		var data_length = projectStandarts.data.length;
		
		for(var i = 0; i < data_length; i++){
			
			var standart = projectStandarts.data[i];

			//Проверка есть ли в списке работ текущая работа
			if(!standart.hasOwnProperty("typeofwork_guid")) continue;

			var work = $("#tableWorks tr input[name^=\'RaportWork\'][name$=\'[work_guid]\'][value=\'"+standart.typeofwork_guid+"\']");

			// console.log(work);
			if(!work.length) continue;

			var tr = $("<tr/>");
			tr.append($("<td/>").addClass("std_work").attr("data-guid",standart.typeofwork_guid).text(standart.typeofwork_name));
			

			//Объем
			tr.append($("<td/>").addClass("std_common_square").text(null));
			
			//Норматив
			tr.append($("<td/>").addClass("std").text(standart.standard));
			
			//Сред расход
			tr.append($("<td/>").addClass("avg_spent"));
			
			//Отклонение
			tr.append($("<td/>").addClass("std_offset").text(standart.standard));
			
			$("#tableProjectStandarts tbody").append(tr);
		};

	};

	var calcAVGStandard = function(){

		var trs = $("#tableProjectStandarts tbody tr");

		if(!trs.length){
			drawProjectStandart();
			trs = $("#tableProjectStandarts tbody tr");
		}

		if(!trs.length) return; 
		

		trs.each(function(){
			var tr = $(this);
			var guid = tr.find(".std_work").attr("data-guid");
			if(!guid) return;

			var works = $("#tableWorks tr input[name^=\'RaportWork\'][name$=\'[work_guid]\'][value=\'"+guid+"\']");
			var works_calcsquare = 0;
				
			if(!works.length) return; 

			works.each(function(){
				works_calcsquare += parseFloat($(this).parents("tr").find(".td_squaremeter input").val());
			});
			
			var nomen = works.eq(0).parents("td").find("input.work_assigned_nomencaltures").val();
			if(!nomen.length) return;
			
			var arr_nomen = nomen.split("|");
			if(!arr_nomen.length) return;
			
			var materials_spent = 0;

			for(var i=0; i < arr_nomen.length;i++){
				var rem = $("#tableRemnants input[name$=\'[nomenclature_guid]\'][value=\'"+arr_nomen[i]+"\']");
				if(!rem.length) continue;
				materials_spent +=parseFloat(rem.parents("tr").find(".tableRemnant_spent input").val());
			}

			if(works_calcsquare <= 0) return;
			materials_spent = materials_spent > 0 ? materials_spent : 0;
			var avg = parseFloat(materials_spent/works_calcsquare);
			var std = parseFloat(tr.find(".std").text());
			// console.log(materials_spent+"/"+works_calcsquare+"="+avg);
			//Объем общий
			tr.find(".std_common_square").text(works_calcsquare.toFixed(3));

			tr.find(".avg_spent").text(avg.toFixed(3));
			var offset = parseFloat(std - avg).toFixed(3);
			tr.find(".std_offset").text(offset).css("color",offset > 0 ? "green" : "red");
		});
	}

	$("body").on("change","input.work_assigned_nomencaltures",function(){
		updateRemnantsTable();
		drawProjectStandart();
	});


	$("body").on("change","input[name=\'Raport[project_guid]\']",function(){
		loadStandars();
	});

		

JS;


$this->registerJs($script);
?>