<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\dictionaries\{ExchangeStatuses,DocumentTypes};
/* @var $this yii\web\View */

$this->title = 'Мои материалы';
?>

<div class="row">
	<div class="col-md-4">
        <div class="row">
            <div class="col-md-12">
                <?php echo Html::a("Создать заявку на получение материала",['material/form'],['class'=>'btn btn-success btn-lg']);?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" style="margin-top:25px; ">
                <?php echo Html::a("Создать документ на перевод материала",['transfer-materials/form'],['class'=>'btn btn-success btn-lg']);?>
            </div>
        </div>
	</div>
    <div class="col-md-8">
        <h3>Заявки на метериал:</h3>
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
<div class="row raports_list">
	<div class="col-md-7">
        <h3>Движение материала:</h3>
        <table class="table table-bordered table-collapsed table-hover">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Номер</th>
                    <th>Статус</th>
                    <th>Вид движения</th>
                    <th>Вид документа</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    if(is_array($unExportedDocs)){
                        foreach ($unExportedDocs as $key => $req) {
                            if(!isset($req['params_in'])) continue;
                            $item = json_decode($req['params_in'],1);
                ?>
                    <tr>
                        <td><?php echo date("d.m.Y",strtotime($item['date']));?></td>
                        <td><?php echo "";?></td>
                        <td><?php echo $item['status'];?></td>
                        <td><?php echo "Расход";?></td>
                        <td><?php echo DocumentTypes::getLabels(DocumentTypes::TYPE_TRANSFER);?></td>
                        <td>
                            <?php echo Html::a("Изменить",['transfer-materials/form','request'=>$req['id']]);?>
                        </td>
                    </tr>
                <?php
                        }
                    }
                ?>
                <?php 
                    if(is_array($documents)){
                        foreach ($documents as $key => $item) {
                ?>
                    <tr>
                        <td><?php echo date("d.m.Y",strtotime($item['date']));?></td>
                        <td><?php echo $item['number'];?></td>
                        <td><?php echo $item['status'];?></td>
                        <td><?php echo $item['movement_type'];?></td>
                        <td><?php echo $item['type_of_operation'];?></td>
                        <td>
                            <?php echo Html::a("Открыть",['document/open','guid'=>$item['guid'],'movement_type'=>$item['movement_type']]);?>
                        </td>
                    </tr>
                <?php
                        }
                    }
                ?>
            </tbody>
        </table>
        <?php
            //echo "<PRE>";
            //print_r($documents);
            //echo "</PRE>";
        ?>
	</div>
    <div class="col-md-5">
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
                    if(is_array($remnants)){
                        foreach ($remnants as $key => $item) {
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
