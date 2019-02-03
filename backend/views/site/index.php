<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\models\User;

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
                    'template' => '{change-status}',
                    'buttons' =>[
	                        'change-status' => function ($url, $model) {
	                        	$title = $model->status == User::STATUS_ACTIVE ? "Перевести в архив" : "Восстановить"; 
	                        	
	                        	return Html::beginForm(['/user/change-status'], 'post')
	                        		. Html::hiddenInput("id",$model->id)
	                        		.Html::a('<i class="glyphicon glyphicon-eye-open"></i>', Url::to(['/user/view', 'id' => $model['id']]),['title' => Yii::t('yii', 'Подробнее')])
                                    . Html::submitButton(
                                            $title,
                                            ['class' => 'btn btn-link','data-confirm'=>'Подтвердите свои действия']
                                    )
                                    . Html::endForm(); 
	                        },
	                    ]
                ]
			],
	]);
?>


