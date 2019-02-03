<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\dictionaries\RaportStatuses;
use yii\bootstrap\ActiveForm;

use common\models\User;


$this->title = 'Бригадир: '.$model->name;


$brigadeConsist = $model->brigadeConsist;
$actualBrigadeRemnants = $model->getActualBrigadeRemnants(false);

?>

<div class="row">
	<div class="col-md-4">
		<h3>Данные:</h3>
		
		<table class="table table-bordered table-collapsed table-hover">
			<tbody>
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
				<tr>
					<td>
						<strong>Смена пароля</strong>
					</td>
					<td>
						<?php $form = ActiveForm::begin(['action'=>['user/change-user-password']])?>
						<?php echo $form->field($changePassModel,'password')->passwordInput()->label(false);?>
						<?php echo $form->field($changePassModel,'user_id')->hiddenInput(['value'=>$model['id']])->label(false);?>
						<?php echo Html::submitButton("Сменить пароль бригадира",['class'=>'btn btn-sm btn-primary','data-confirm'=>"Подтвердите ваши действия!"]);?>

						<?php ActiveForm::end();?>
					</td>
				</tr>
				<tr>
					<td>
						<strong>
							Cтатус (
						<?php
							echo $model->status == User::STATUS_ACTIVE ? "Активный" : "В архиве"; 
						?>
						)
						</strong>
					</td>
					<td>
						<?php 
							$btn = "btn-danger";
							$title = "Перевести в архив";
							if($model->status != User::STATUS_ACTIVE){
								$btn = "btn-success";
								$title = "Восстановить пользователя";
							}
							echo Html::beginForm(['/user/change-status'], 'post')
	                        		. Html::hiddenInput("id",$model->id)
                                    . Html::submitButton(
                                            $title,
                                            ['class' => 'btn btn-sm '.$btn,'data-confirm'=>'Подтвердите свои действия!']
                                    )
                                    . Html::endForm();
						?>
					</td>
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
	<div class="col-md-5">
		<h3>Рапорты бригадира:</h3>
	</div>
</div>
<div class="row" style="margin-top: 10px;">
	<div class="col-md-12">
		<?php
            echo \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $RaportFilter,
                'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
                'tableOptions'=>['class'=>'table table-striped table-bordered table-hover'],
                'showFooter'=>false,
                'summary'=> "",
                'rowOptions'=>function($model){
                    $ops = [];

                    $ops['class']=array_key_exists($model->status, RaportStatuses::$notification) ? RaportStatuses::$notification[$model->status] : "";

                    return $ops;
                },
                'columns'=>[
                	// ['class'=>'yii\grid\SerialColumn'],
                    [
                        'attribute'=>"number",
                        "value"=>"number",
                    ],
                    [
                        'attribute'=>"created_at",
                        'value'=>function($m){
                        	return date("d.m.Y",strtotime($m['created_at']));
                        },
                        'filter'=>Html::dropDownList("RaportFilter[month]",$RaportFilter->month,$RaportFilter::getMonths(),['class'=>'form-control input-sm','prompt'=>'Выберите месяц'])
                    ],
                    [
                        'attribute'=>"object_guid",
                        'value'=>function($m){
                        	$object = $m->object;
                        	return isset($object->id) ? $object['name'] : "";
                        },
                    ],
                    [
                        'attribute'=>"project_guid",
                        'value'=>function($m){
                        	$project = $m->project;
                        	return isset($project->id) ? $project['name'] : "";
                        },
                    ],
                    [
                        'attribute'=>"boundary_guid",
                        'value'=>function($m){
                        	$object = $m->object;
                        	$boundary = isset($object->id) ? $object->boundary : null;
                        	return isset($boundary->id) ? $boundary['name'] : "";
                        },
                    ],
                    [
                        'attribute'=>"status",
                        'value'=>function($m){
                        	return $m->statusTitle;
                        },
                        'format'=>'raw'
                    ],
                  
                    ['class' => 'yii\grid\ActionColumn',
                        'template' => '{view}&nbsp{update}',
                        'buttons' =>
                        [
                            'view' => function ($url, $model) {
                                return  Html::a('<i class="glyphicon glyphicon-eye-open"></i>', Url::to(['/raport/view', 'id' => $model->id]), [
                                     'title' => Yii::t('yii', 'Посмотреть')
                                ]); 
                            },
                            'update' => function ($url, $model) {
                                return Html::a('<i class="glyphicon glyphicon-pencil"></i>', Url::to(['/raport/form', 'id' => $model->id]), [
                                     'title' => Yii::t('yii', 'Изменить')
                                ]); 
                            }, 
                        ]
                    ]
                ]
            ]);
        ?>
	</div>
</div>

