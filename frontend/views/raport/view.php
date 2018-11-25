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

$this->title = "Рапорт";

?>

<div class="row">
	<div class="col-md-12">
		<?php if($model->isCanUpdate){
			echo Html::a("Изменить",['raport/form','id'=>$model->id],['class'=>'btn btn-primary pull-right']);
		}?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="row">
				<div class="col-md-12">
					<ul class="nav nav-tabs">	
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
											<p><?php echo $master_name?></p>
											<p><?php echo $model['created_at'];?></p>
											<p><?php echo $model['starttime'];?></p>
											<p><?php echo $model['endtime'];?></p>
										</div>
										<div class="col-md-6 object_form">
											<div class="row">
												<div class="col-md-12">
													<p><?php echo $object_name?></p>
												</div>
												<div class="col-md-12">
													<p><?php echo $project_name?></p>
												</div>
												<div class="col-md-12">
													<label>Округ</label>
													<p><?php echo $boundary_name?></p>
												</div>
											</div>
										</div>
									</div>
									<hr>
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class="col-md-6">
													<p><?php echo $model['temperature_start'];?></p>
													<p><?php echo $model['surface_temperature_start'];?></p>
													<p><?php echo $model['airhumidity_start'];?></p>
												</div>
												<div class="col-md-6">
													<p><?php echo $model['temperature_end'];?></p>
													<p><?php echo $model['surface_temperature_end'];?></p>
													<p><?php echo $model['airhumidity_end'];?></p>
												</div>
											</div>
										</div>
									</div>
									<hr>
									<div class="row">
										<div class="col-md-6">
											<p><?php echo $model['comment'];?></p>
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
											</tr>
										</thead>
										<tbody>
											<?php if(is_array($BrigadeConsist)){
												foreach ($BrigadeConsist as $key => $item) {
											?>
												<tr>
													<td><?php echo 1+$key;?></td>
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
											</tr>
										</thead>
										<tbody>
											<?php if(is_array($RaportWorks)){?>
												<?php foreach ($RaportWorks as $key => $item) {?>
												<tr>
													<td><?php echo $key+1; ?></td>
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

													<td><?php echo $item['nomenclature_name'];?></td>
													<td><?php echo $item['was'];?></td>
													<td><?php echo $item['spent'];?></td>
													<td><?php echo $item['rest'];?></td>
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
							<div class="row">
								<?php if(is_array($RaportFiles)){
									$images = RaportFile::getImageTypes();
									foreach ($RaportFiles as $key => $item) {
								?>
									<div class="col-md-3">
										<?php 
											$filePath = "tmp/".$item['file'];
											if(in_array($item['file_type'], $images)){
												
												if(!file_exists($filePath)){
													$f = fopen($filePath, "w+");
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

