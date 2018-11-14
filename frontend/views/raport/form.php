<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = "Форма рапорта";
?>
<?php $form = ActiveForm::begin();?>
<div class="row">
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-6">
				<?php echo Html::submitButton("Сохранить",['class'=>'btn btn-primary']);?>
			</div>
		</div>
		<div class="row">
				<div class="col-md-12">
					<ul class="nav nav-tabs">	
					  <li class="active"><a data-toggle="tab" href="#base">Основное</a></li>
					  <li><a data-toggle="tab" href="#consist">Состав бригады</a></li>
					  <li><a data-toggle="tab" href="#works">Характеристики работ</a></li>
					  <li><a data-toggle="tab" href="#remnants">Остатки</a></li>
					</ul>
					<div class="tab-content">
						
						<div id="base" class="tab-pane fade in active">
							<h3>Основное</h3>
							<div class="row">
								<div class="col-md-12">
									
								</div>
							</div>
						</div>

						<div id="consist" class="tab-pane fade in">
							<h3>Состав бригады</h3>
							<div class="row">
								<div class="col-md-12">
									
								</div>
							</div>
						</div>

						<div id="works" class="tab-pane fade in">
							<h3>Характеристики работ</h3>
							<div class="row">
								<div class="col-md-12">
									
								</div>
							</div>
						</div>

						<div id="remnants" class="tab-pane fade in">
							<h3>Остатки</h3>
							<div class="row">
								<div class="col-md-12">
									
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<nav aria-label="...">
							  <ul class="pager">
							    <li class="prev_tab"><a>Назад</a></li>
							    <li class="next_tab"><a>Далее</a></li>
							  </ul>
							</nav>
						</div>
					</div>
				</div>
		</div>
	</div>
</div>
<?php 


$script = <<<JS
	$("body").on("click",".pager li",function(){
		var active_tab = $(".nav-tabs li.active");
		if($(this).hasClass("prev_tab")){
			if(active_tab.length){
				var prev = active_tab.prev();
				prev.length ? prev.find("a").trigger("click") : null;
			}
		}else if($(this).hasClass("next_tab")){
			if(active_tab.length){
				var next = active_tab.next();
				next.length ? next.find("a").trigger("click") : null;
			}
		}
	});
JS;


$this->registerJs($script);
?>