<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
/* @var $this yii\web\View */

$this->title = 'Мои рапорта';
?>

<div class="row">
	<div class="col-md-2">
		<label class="form-label">Месяц</label>
		<select class="form-control">
			<option>Октябрь</option>
		</select>
	</div>
	<div class="col-md-2 text-right">
		<button class="btn btn-success" style="margin-top: 25px;">Фильтровать</button>
	</div>
</div>
<div class="row" style="margin-top: 10px;">
	<div class="col-md-12">
		<?php 

        echo \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $modelFilters,
                'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
                'tableOptions'=>['class'=>'table table-striped table-bordered table-hover'],
                'showFooter'=>true,
                'columns'=>[
                	['class'=>'yii\grid\SerialColumn'],
                    [
                        'attribute'=>"number",
                        "value"=>"number",
                    ],
                    [
                        'attribute'=>"created_at",
                        'value'=>function($m){
                        	return date("d.m.Y",strtotime($m['created_at']));
                        },
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
                    ],
                  
                    ['class' => 'yii\grid\ActionColumn',
                        'template' => '{update}',
                        'buttons' =>
                         [
                             
                            'update' => function ($url, $model) {
                                return $model->isCanUpdate ? Html::a('Изменить', Url::to(['/raport/form', 'id' => $model->id]), [
                                     'title' => Yii::t('yii', 'Изменить')
                                ]) : ""; 
                            }, 
                         ]
                    ]
                ]
            ]);
        ?>
	</div>
</div>
