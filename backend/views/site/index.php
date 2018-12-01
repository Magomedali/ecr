<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

$this->title = 'Бригадиры';
?>
<?php

	echo GridView::widget([
			'dataProvider'=>$dataProvider,
			'filterModel'=>$UserSearch,
			'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
			'tableOptions' => [
            	'id'=>'brigadiers','class'=>'table table-striped table-bordered'
        	],
        	'summary'=>'',
			'columns'=>[
				['class'=>'yii\grid\SerialColumn'],
				[
					'attribute'=>'login',
					'value'=>function ($m) {
                        return $m['login'];
                    }
				],
				[
					'attribute'=>'name',
					'value'=>function ($m) {
                        return $m['name'];
                    }
				],
				[
					'attribute'=>'ktu',
					'value'=>function ($m) {
                        return $m['ktu'];
                    }
				],
				['class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'buttons' =>[
	                        'view' => function ($url, $model) {
	                            return  Html::a('<i class="glyphicon glyphicon-eye-open"></i>', Url::to(['/user/view', 'id' => $model['id']]),['title' => Yii::t('yii', 'Подробнее')]); 
	                        } 
	                    ]
                ]
			],
	]);
?>


