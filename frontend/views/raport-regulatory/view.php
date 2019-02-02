<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\models\User;
use common\widgets\autocomplete\AutoComplete;

$user = Yii::$app->user->identity;

$master = isset($model->master_guid) ? $model->master : null;
$master_name = isset($master->id) ? $master->name : null;

$RaportWorks = $model->works;


$this->title = "Регламентный рапорт " . $model->number;
$this->params['backlink']['url']=Url::to(['raport/index']);

?>
<div class="row">
	<div class="col-md-12">
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
			</div>
		</div>

						


		<!-- Характеристики работ -->
		<div class="row">
			<div class="col-md-12">
				<h3>Виды работ</h3>
				<table id="tableWorks" class="table table-bordered table-hovered table-collapsed vertical-table">
					<thead>
						<tr>
							<td>Физ. лицо</td>
							<td>Вид работы</td>
							<td>Количество часов</td>
						</tr>
					</thead>
					<tbody>
						<?php if(is_array($RaportWorks)){?>
							<?php foreach ($RaportWorks as $key => $item) {?>
								<tr>
									<td>
										<?php echo $item['user_name'];?>
									</td>
									<td>
										<?php echo $item['work_name'];?>
									</td>
									<td><?php echo $item['hours'];?></td>
								</tr>
							<?php } ?>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

