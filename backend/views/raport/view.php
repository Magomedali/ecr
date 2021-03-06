<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\models\User;
use common\widgets\autocomplete\AutoComplete;
use common\models\RaportWork;
use common\models\RaportFile;

$user = Yii::$app->user->identity;

$object = $model->object_guid ? $model->object : null;
$object_name = isset($object->id) ? $object->name : "";

$boundary = isset($object->id) && $object->boundary_guid ? $object->boundary : null;
$boundary_name = isset($boundary->id) ? $boundary->name : "";

$project = isset($model->project_guid) ? $model->project : null;
$project_name = isset($project->id) ? $project->name : null;

$master = isset($model->master_guid) ? $model->master : null;
$master_name = isset($master->id) ? $master->name : null;

$BrigadeConsist = $model->consist;
$ActualBrigadeRemnants = $model->materials;
$RaportWorks = $model->works;
$RaportFiles = $model->files;

if($model->isWrongMaterials($ActualBrigadeRemnants)){
	Yii::$app->session->setFlash("warning","В остатках недостаточное количество материалов!");
}

$this->title = "Рапорт " . $model->number;

?>
<div class="row">
	<div class="col-md-12">
		<div class="row">
				<div class="col-md-12">
					<ul class="nav nav-tabs vertical-tablet">	
					  <li class="active"><a data-toggle="tab" href="#base">Основное</a></li>
					  <li><a data-toggle="tab" href="#consist">Состав бригады</a></li>
					  <li><a data-toggle="tab" href="#works">Характеристики работ</a></li>
					  <li><a data-toggle="tab" href="#remnants" >Остатки</a></li>
					  <li><a data-toggle="tab" href="#files" >Файлы</a></li>
					</ul>
					<div class="tab-content">

						<!-- Основное -->
						<div id="base" class="tab-pane fade in active">
							<h3>Основное</h3>
							<div class="row">
								<div class="col-md-12">
									<div class="row">
										<div class="col-md-6">
											<table class="table table-collapsed">
												<tr>
													<td>
														<strong>
															<?php echo $model->getAttributeLabel("master_guid")?>
														</strong>
													</td>
													<td><?php echo $master_name?></td>
												</tr>
												<tr>
													<td>
														<strong>
															<?php echo $model->getAttributeLabel("created_at")?>
														</strong>
													</td>
													<td><?php echo $model['created_at'];?></td>
												</tr>
												<tr>
													<td>
														<strong>
															<?php echo $model->getAttributeLabel("starttime")?>
														</strong>
													</td>
													<td><?php echo $model['starttime']?></td>
												</tr>
												<tr>
													<td>
														<strong>
															<?php echo $model->getAttributeLabel("endtime")?>
														</strong>
													</td>
													<td><?php echo $model['endtime']?></td>
												</tr>
											</table>
										</div>
										<div class="col-md-6 object_form">
											<table class="table table-collapsed">
												<tr>
													<td>
														<strong>
															<?php echo $model->getAttributeLabel("object_guid")?>
														</strong>
													</td>
													<td><?php echo $object_name?></td>
												</tr>
												<tr>
													<td>
														<strong>
															<?php echo $model->getAttributeLabel("project_guid")?>
														</strong>
													</td>
													<td><?php echo $project_name;?></td>
												</tr>
												<tr>
													<td>
														<strong>
															<?php echo $model->getAttributeLabel("boundary_guid")?>
														</strong>
													</td>
													<td><?php echo $boundary_name?></td>
												</tr>
												<tr>
													<td>
														<strong>
															<?php echo $model->getAttributeLabel("comment")?>
														</strong>
													</td>
													<td><?php echo $model['comment']?></td>
												</tr>
											</table>
										</div>
									</div>
									<hr>
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class="col-md-6">
													<table class="table table-collapsed">
														<tr>
															<td>
																<strong>
																	<?php echo $model->getAttributeLabel("temperature_start")?>
																</strong>
															</td>
															<td><?php echo $model['temperature_start']?></td>
														</tr>
														<tr>
															<td>
																<strong>
																	<?php echo $model->getAttributeLabel("surface_temperature_start")?>
																</strong>
															</td>
															<td><?php echo $model['surface_temperature_start'];?></td>
														</tr>
														<tr>
															<td>
																<strong>
																	<?php echo $model->getAttributeLabel("airhumidity_start")?>
																</strong>
															</td>
															<td><?php echo $model['airhumidity_start']?></td>
														</tr>
													</table>
												</div>
												<div class="col-md-6">
													<table class="table table-collapsed">
														<tr>
															<td>
																<strong>
																	<?php echo $model->getAttributeLabel("temperature_end")?>
																</strong>
															</td>
															<td><?php echo $model['temperature_end']?></td>
														</tr>
														<tr>
															<td>
																<strong>
																	<?php echo $model->getAttributeLabel("surface_temperature_end")?>
																</strong>
															</td>
															<td><?php echo $model['surface_temperature_end'];?></td>
														</tr>
														<tr>
															<td>
																<strong>
																	<?php echo $model->getAttributeLabel("airhumidity_end")?>
																</strong>
															</td>
															<td><?php echo $model['airhumidity_end']?></td>
														</tr>
													</table>
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
												<td>Физ.лицо</td>
												<td>Техника</td>
												<td>КТУ</td>
											</tr>
										</thead>
										<tbody>
											<?php if(is_array($BrigadeConsist)){
												foreach ($BrigadeConsist as $key => $item) {
											?>
												<tr>
													<td>
														<p><?php echo $item['user_name'];?></p>
													</td>
													<td>
														<p><?php echo $item['technic_name'];?></p>
													</td>
													<td class="person_ktu">
														<span><?php echo $item['user_ktu'];?></span>
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
						</div>


						<!-- Характеристики работ -->
						<div id="works" class="tab-pane fade in">
							<h3>Характеристики работ</h3>
							<div class="row">
								<div class="col-md-12">
									<table id="tableWorks" class="table table-bordered table-hovered table-collapsed vertical-table">
										<thead>
											<tr>
												<td>Вид работы</td>
												<td>Линия</td>
												<td>Механизированная</td>
												<td>П.М./Шт</td>
												<td>Количество</td>
												<td>Процент сохранности</td>
												<td>кв. м</td>
											</tr>
										</thead>
										<tbody>
											<?php if(is_array($RaportWorks)){?>
												<?php foreach ($RaportWorks as $key => $item) {?>
												<tr>
													<td>
														<?php echo $item['work_name'];?>
													</td>
													<td>
														<?php echo $item['line_name'];?>
													</td>
													<td>
														<?php echo (int)$item['mechanized'] ? "Да" : "Нет";?>
													</td>
													<td>
														<?php echo $item['length'];?>
													</td>
													<td><?php echo $item['count'];?></td>
													<td><?php echo $item['percent_save'] ? $item['percent_save'] : null;?></td>
													<td><?php echo $item['squaremeter'];?></td>
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
												<td>Израсходовано</td>
											</tr>
										</thead>
										<tbody>
											<?php if(is_array($ActualBrigadeRemnants)){
												foreach ($ActualBrigadeRemnants as $key => $item) {
											?>
												<tr>
													<td><?php echo $item['nomenclature_name'];?></td>
													<td><?php echo $item['spent'];?></td>
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



						<!-- Файлы -->
						<div id="files" class="tab-pane fade in">
							<h3>Файлы</h3>
							<div class="row formAddFiles">
								<?php $form = ActiveForm::begin(['id'=>'addFileForm','action'=>['raport/add-files'],'options'=>['enctype'=>'multipart/form-data']])?>
								<div class="col-md-4">
									<?php 
										echo Html::fileInput("files[]",null,['multiple'=>true,'id'=>'fileInput']);
										echo Html::hiddenInput('model_id',$model->id);
									?>
								</div>
								<div class="col-md-5">
									<?php 
										echo Html::submitButton("Добавить",['class'=>"btn btn-primary"]);
									?>
								</div>
								<?php

									$js = <<<JS

										$("#addFileForm").submit(function(event){
											if(!$("#fileInput").val()){
												$("#fileInput").addClass("fieldHasError");
												event.preventDefault();
											}else{
												$("#fileInput").removeClass("fieldHasError");
											}
										});
JS;

									$this->registerJs($js);
								?>
								<?php ActiveForm::end();?>
							</div>
							<div class="row">
								<?php if(is_array($RaportFiles)){
									$images = RaportFile::getImageTypes();
									foreach ($RaportFiles as $key => $item) {
								?>
									<div class="col-md-3 file_item" >
										<?php 
											$filePath = "tmp/".$item['file'];
											if(in_array($item['file_type'], $images)){
												if(!file_exists($filePath) || 1){
													$f = fopen($filePath, "w");
													fwrite($f, $item['file_binary']);
													fclose($f);
												}
												echo Html::img($filePath);
											}else{
												echo Html::a($item['file_name'],['raport/read-file','id'=>$item['id']],['target'=>'_blank']);
											}
										?>
									</div>		
								<?php
										}
									}
								?>
								
							</div>
						</div>
					</div>
				</div>
		</div>
	</div>
</div>

