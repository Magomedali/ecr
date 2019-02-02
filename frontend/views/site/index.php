<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\models\TypeOfWork;

/* @var $this yii\web\View */

$this->title = 'Мои кабинет';
?>

<div class="row">
	<div class="col-md-4">
		<h3>Данные:</h3>
		<table class="table table-bordered table-collapsed table-hover">
			<tbody>
				<tr>
					<td><strong><?php echo "Бригада";?></strong></td>
					<td><?php echo $model->brigade ? $model->brigade->name : "";?></td>
				</tr>
				<tr>
					<td><strong><?php echo $model->getAttributeLabel('login');?></strong></td>
					<td><?php echo $model['login'];?></td>
				</tr>
				<tr>
					<td><strong><?php echo $model->getAttributeLabel('name');?></strong></td>
					<td><?php echo $model['name'];?></td>
				</tr>
				<tr>
					<td><strong><?php echo $model->getAttributeLabel('ktu');?></strong></td>
					<td><?php echo $model['ktu'];?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-md-4">
		<h3>Состав бригады:</h3>
		<table class="table table-bordered table-collapsed table-hover">
			<thead>
				<tr>
					<th>Ф.И.О</th>
					<th>Техника</th>
					<th>КТУ</th>
				</tr>
			</thead>
			<tbody>
				<?php 
					if(is_array($brigadeConsist)){
						foreach ($brigadeConsist as $key => $item) {
				?>
					<tr>
						<td><?php echo $item['user_name'];?></td>
						<td><?php echo $item['technic_name'];?></td>
						<td><?php echo $item['user_ktu'];?></td>
					</tr>
				<?php
						}
					}
				?>
			</tbody>
		</table>
	</div>
	<div class="col-md-4">
		<h3>Остатки:</h3>
		<table class="table table-bordered table-collapsed table-hover">
			<thead>
				<tr>
					<th>Номенклатура</th>
					<th>Количество</th>
				</tr>
			</thead>
			<tbody>
				<?php 
					if(is_array($actualBrigadeRemnants)){
						foreach ($actualBrigadeRemnants as $key => $item) {
				?>
					<tr>
						<td><?php echo $item['nomenclature_name'];?></td>
						<td><?php echo $item['was'];?></td>
					</tr>
				<?php
						}
					}
				?>
			</tbody>
		</table>
	</div>
</div>


<div class="row">
	<div class="col-md-6">
        <h3>Выполненый объем работы:</h3>
    </div>
</div>
<div class="row totals_list">
	<div class="col-md-6">
		<?php
            echo \yii\grid\GridView::widget([
                'dataProvider' => $dataProviderTotalOutput,
                'filterModel' => $TotalOutputFilter,
                'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
                'tableOptions'=>['class'=>'table table-striped table-bordered table-sm table-hover vertical-table'],
                'showFooter'=>false,
                'summary'=> "",
                'emptyText'=>"Список пуст",
                'rowOptions'=>function($item){
                    $ops = [];
                    return $ops;
                },
                'columns'=>[
                    [
                        'attribute'=>"work",
                        "label"=>"Вид работы",
                        'value'=>function($item){
                        	if(!isset($item['type_of_work_guid'])) return "Неизвестный вид работы";

                        	$m = TypeOfWork::findOne(['guid'=>$item['type_of_work_guid']]);

                        	if(!isset($m->name)) return "Неизвестный вид работы";
                        	return $m->name;
                        },
                    ],
                    [
                        'attribute'=>"status",
                        "label"=>"Кв. м.",
                        'value'=>function($item){
                        	return $item['square'];
                        },
                    ],
                  
                    
                ]
            ]);
        ?>
	</div>
</div>
