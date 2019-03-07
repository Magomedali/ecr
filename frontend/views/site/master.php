<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\dictionaries\ExchangeStatuses;
use common\dictionaries\AppStatuses;

/* @var $this yii\web\View */

$this->title = 'Кабинет мастера';
?>

<div class="row raports_list">
    <div class="col-md-12">
        <h3>Сдельные рапорта:</h3>
    </div>
	<div class="col-md-12">
		<?php
            echo \yii\grid\GridView::widget([
                'dataProvider' => $dataProviderRaport,
                'filterModel' => $RaportFilter,
                'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
                'tableOptions'=>['class'=>'table table-striped table-bordered table-sm table-hover vertical-table'],
                'showFooter'=>false,
                'summary'=> "",
                'layout'=>"{items}",
                'emptyText'=>"Список пуст",
                'rowOptions'=>function($model){
                    $ops = [];

                    $ops['class']=array_key_exists($model->status, ExchangeStatuses::$notification) ? ExchangeStatuses::$notification[$model->status] : "";

                    return $ops;
                },
                'columns'=>[
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




<div class="row raports_list">
    <div class="col-md-12">
        <h3>Регламентные рапорта:</h3>
    </div>
    <div class="col-md-12">
        <?php
            echo \yii\grid\GridView::widget([
                'dataProvider' => $dataProviderRaportRegulatory,
                'filterModel' => $RaportRegulatoryFilter,
                'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
                'tableOptions'=>['class'=>'table table-striped table-bordered table-sm table-hover vertical-table'],
                'showFooter'=>false,
                'summary'=> "",
                'layout'=>"{items}",
                'emptyText'=>"Список пуст",
                'rowOptions'=>function($model){
                    $ops = [];

                    $ops['class']=array_key_exists($model->status, ExchangeStatuses::$notification) ? ExchangeStatuses::$notification[$model->status] : "";

                    return $ops;
                },
                'columns'=>[
                    [
                        'attribute'=>"number",
                        "value"=>"number",
                    ],
                    [
                        'attribute'=>"created_at",
                        'value'=>function($m){
                            return date("d.m.Y",strtotime($m['created_at']));
                        },
                        'filter'=>Html::dropDownList("RaportRegulatoryFilter[month]",$RaportRegulatoryFilter->month,$RaportRegulatoryFilter::getMonths(),['class'=>'form-control input-sm','prompt'=>'Выберите месяц'])
                    ],
                    [
                        'attribute'=>"workers",
                        'label'=>'Исполнители',
                        'value'=>function($m){
                            return $m->workers;
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
                                return  Html::a('<i class="glyphicon glyphicon-eye-open"></i>', Url::to(['/raport-regulatory/view', 'id' => $model->id]), [
                                     'title' => Yii::t('yii', 'Посмотреть')
                                ]); 
                            },
                            'update' => function ($url, $model) {
                                return $model->isCanUpdate ? Html::a('<i class="glyphicon glyphicon-pencil"></i>', Url::to(['/raport-regulatory/form', 'id' => $model->id]), [
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

<div class="row raports_list">
    <div class="col-md-12">
        <h3>Заявки на материал:</h3>
    </div>
    <div class="col-md-12">
        <?php
            echo \yii\grid\GridView::widget([
                'dataProvider' => $dataProviderMaterialApp,
                'filterModel' => $MaterialAppFilter,
                'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
                'tableOptions'=>['class'=>'table table-striped table-bordered table-sm table-hover vertical-table'],
                'showFooter'=>false,
                'summary'=> "",
                'layout'=>"{items}",
                'emptyText'=>"Список пуст",
                'rowOptions'=>function($model){
                    $ops = [];

                    $ops['class']=array_key_exists($model->status, AppStatuses::$notification) ? AppStatuses::$notification[$model->status] : "";

                    return $ops;
                },
                'columns'=>[
                    [
                        'attribute'=>"number",
                        "value"=>"number",
                    ],
                    [
                        'attribute'=>"created_at",
                        'value'=>function($m){
                            return date("d.m.Y",strtotime($m['created_at']));
                        },
                        'filter'=>Html::dropDownList("MaterialAppFilter[month]",$MaterialAppFilter->month,$MaterialAppFilter::getMonths(),['class'=>'form-control input-sm','prompt'=>'Выберите месяц'])
                    ],
                    [
                        'attribute'=>"user_guid",
                        'value'=>function($m){
                            $user = $m->getUser();
                            return $user ? $user['name'] : "";
                        },
                    ],
                    [
                        'attribute'=>"status",
                        'value'=>function($m){
                            return $m->statusTitle;
                        },
                    ],
                  
                    ['class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'buttons' =>
                        [
                            'view' => function ($url, $model) {
                                return Html::a('Открыть', Url::to(['/material/open', 'id' => $model->id]), [
                                     'title' => Yii::t('yii', 'Открыть')
                                ]);
                            }, 
                        ]
                    ]
                ]
            ]);
        ?>
    </div>
</div>
