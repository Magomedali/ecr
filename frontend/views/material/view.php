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

$stockroom = $model->stockroom_guid ? $model->stockroom : null;
$stockroom_name = isset($stockroom->id) ? $stockroom->name : "";

$master = $model->master_guid ? $model->master : null;
$master_name = isset($master->id) ? $master->name : "";

$materialsAppItem = $model->getMaterialsAppItems();

$this->title = "Заявка " . $model->number;
$this->params['backlink']['url']=Url::to(['material/index']);
$this->params['backlink']['confirm']=false;
?>
<div class="row">
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-4">
				<p><strong>Дата создания заявки</strong></p>
				<p>
					<?php echo date("d.m.Y\TH:i:s",strtotime($model['created_at']));?>
				</p>
			</div>
			<div class="col-md-4">
				<p><strong>Склад:</strong></p>
				<p>
					<?php echo $stockroom_name;?>
				</p>
			</div>
			<div class="col-md-4">
				<p><strong>Мастер:</strong></p>
				<p>
					<?php echo $master_name;?>
				</p>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<table id="tableMaterials" class="table table-bordered table-hovered table-collapsed">
					<thead>
						<tr>
							<td>Номенклатура</td>
							<td>Количество</td>
							<td>Единица Измерения</td>
						</tr>
					</thead>
					<tbody>
					<?php if(is_array($materialsAppItem)){
							foreach ($materialsAppItem as $key => $item) {
						?>
						<tr>
							<td><?php echo $item['nomenclature_name'];?></td>
							<td><?php echo $item['count'];?></td>
							<td><?php echo $item['nomenclature_unit'];?></td>					
						</tr>
					<?php }
						}
					?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

