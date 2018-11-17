<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\models\User;
use common\widgets\autocomplete\AutoComplete;


$masters = User::find()->where(['is_master'=>true])->asArray()->all();
$user = Yii::$app->user->identity;

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

										<div class="col-md-3">
											<div class="row">
												<div class="col-md-12">
													<?php echo $form->field($model,'object_guid')->dropDownList(ArrayHelper::map([],'guid','name'),['prompt'=>'Укажите объект']);?>
												</div>
												<div class="col-md-12">
													<?php echo $form->field($model,'project_guid')->dropDownList(ArrayHelper::map([],'guid','name'),['prompt'=>'Укажите контракт']);?>
												</div>
												<div class="col-md-12">
													<label>Округ</label>
													<?php echo Html::textInput("boundary_name",null,['class'=>'form-control','disabled'=>true]);?>
													
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
												<td><?php echo html::a('+',['raport/get-row-consist'],['class'=>'btn btn-sm btn-primary','id'=>'btnAddConsist'])?></td>
											</tr>
										</thead>
										<tbody>
											
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
					console.log(json);
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
	})

JS;


$this->registerJs($script);
?>