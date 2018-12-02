<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\dictionaries\RaportStatuses;

/* @var $this yii\web\View */

$this->title = 'Мои рапорта';
?>


<div class="row raports_list">
	<div class="col-md-12">
		<?php
            echo \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $modelFilters,
                'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
                'tableOptions'=>['class'=>'table table-striped table-bordered table-sm table-hover vertical-table'],
                'showFooter'=>false,
                'summary'=> "",
                'rowOptions'=>function($model){
                    $ops = [];

                    $ops['class']=array_key_exists($model->status, RaportStatuses::$notification) ? RaportStatuses::$notification[$model->status] : "";

                    if($model->isWrongMaterials()){
                        Yii::$app->session->setFlash("warning","В остатках недостаточное количество материалов!");
                        $ops['class']='warning';
                    }



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
                        'filter'=>Html::dropDownList("RaportFilter[month]",$modelFilters->month,$modelFilters::getMonths(),['class'=>'form-control input-sm','prompt'=>'Выберите месяц'])
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
                        'template' => '{view}&nbsp{update}',
                        'buttons' =>
                        [
                            'view' => function ($url, $model) {
                                return  Html::a('<i class="glyphicon glyphicon-eye-open"></i>', Url::to(['/raport/view', 'id' => $model->id]), [
                                     'title' => Yii::t('yii', 'Посмотреть')
                                ]); 
                            },
                            'update' => function ($url, $model) {
                                return $model->isCanUpdate ? Html::a('<i class="glyphicon glyphicon-pencil"></i>', Url::to(['/raport/form', 'id' => $model->id]), [
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
