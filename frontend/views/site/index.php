<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\dictionaries\RaportStatuses;

/* @var $this yii\web\View */

$this->title = 'Мои кабинет';
?>


<div class="row lk">
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-6">
				
                    <a href="<?php echo Url::to(['/raport/form'])?>" class="btn btn-success">
                        <?php echo Yii::t('site', 'Создать рапорт')?>
                    </a>
                
			</div>
		</div>
	</div>
</div>
