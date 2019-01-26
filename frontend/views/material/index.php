<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\dictionaries\ExchangeStatuses;
/* @var $this yii\web\View */

$this->title = 'Мои материалы';
?>

<div class="row">
	<div class="col-md-12">
		<?php echo Html::a("Создать заявку",['material/form']);?>

        <?php echo Html::a("Создать перевод",['transfer-materials/form']);?>
	</div>
</div>
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
                'layout'=>"{items}",
                'emptyText'=>"Список пуст",
                'rowOptions'=>function($model){
                    $ops = [];

                    $ops['class']=array_key_exists($model->status, ExchangeStatuses::$notification) ? ExchangeStatuses::$notification[$model->status] : "";

                    
                    return $ops;
                },
                'columns'=>[
                    
                    [
                        'attribute'=>"created_at",
                        'value'=>function($m){
                        	return date("d.m.Y H:i:s",strtotime($m['created_at']));
                        },
                        'filter'=>Html::dropDownList("RaportFilter[month]",$modelFilters->month,$modelFilters::getMonths(),['class'=>'form-control input-sm','prompt'=>'Выберите месяц'])
                    ],
                    [
                        'attribute'=>"number",
                        "value"=>"number",
                    ],
                    [
                        'attribute'=>"master_guid",
                        'value'=>function($m){
                            $master = $m->master;
                            return isset($master->id) ? $master['name'] : "";
                        },
                    ],
                    [
                        'attribute'=>"stockroom_guid",
                        'value'=>function($m){
                        	$stockroom = $m->stockroom;
                        	return isset($stockroom->id) ? $stockroom['name'] : "";
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
                                return  Html::a('<i class="glyphicon glyphicon-eye-open"></i>', Url::to(['/material/view', 'id' => $model->id]), [
                                     'title' => Yii::t('yii', 'Посмотреть')
                                ]); 
                            },
                            'update' => function ($url, $model) {
                                return $model->isCanUpdate ? Html::a('<i class="glyphicon glyphicon-pencil"></i>', Url::to(['/material/form', 'id' => $model->id]), [
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
