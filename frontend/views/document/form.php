<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\models\User;
use common\models\Nomenclature;

$this->title = $doc['type_of_operation'];
$this->params['backlink']['url']=Url::to(['material/index']);
?>
<div class="row">
	<div class="col-md-12">
		<?php $form = ActiveForm::begin(['id'=>'commitDocument','action'=>['document/form']]);?>
		<div class="row">
			<div class="col-md-3 form-group">
				<label>Дата создания документа:</label>
				<?php echo Html::input("date",'doc[date]',$doc['date'],['class'=>'form-control','readonly'=>true]);?>
			</div>
			<div class="col-md-3 form-group">
				<label>Номер:</label>
				<?php echo Html::input("text",'doc[number]',$doc['number'],['class'=>'form-control','readonly'=>true]);?>
			</div>
			<div class="col-md-3 form-group">
				<label>Статус:</label>
				<?php 
					echo Html::input("text",'doc[status]',$doc['status'],['class'=>'form-control','readonly'=>true]);
				?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 form-group">
				<?php
					if(isset($doc['interaction_name']) && $doc['interaction_name']){
						?>
						<p><strong>От:</strong></p>
						<p><?php echo $doc['interaction_name'];?></p>
						<p><strong>Комментарии:</strong></p>
						<p><?php echo $doc['comment_interaction'];?></p>
						<?php
					}
				?>
			</div>
			<div class="col-md-3 form-group">
				<label>Вид движения:</label>
				<?php
					echo Html::textInput('movement_type',$doc['movement_type'],['class'=>'form-control','readonly'=>true]);
				?>
			</div>
			<div class="col-md-3 form-group">
				<label>Комментарии:</label>
				<?php
					echo Html::textarea('doc[comment]',$doc['comment'],['class'=>'form-control','readonly'=>true]);
				?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<table class="table table-sm table-bordered table-collapsed table-hovered">
					<tr>
						<th>Номенклатура</th>
						<th>Серия</th>
						<th>Количество</th>
					</tr>
					<?php
						if(isset($doc['materials']) && is_array($doc['materials'])){

							$materials = ArrayHelper::isAssociative($doc['materials']) ? array($doc['materials']) : $doc['materials'];

							foreach ($materials as $key => $item) {
					?>
						<tr>
							<td>
								<?php
									$nomen = Nomenclature::findOne(['guid'=>$item['nomenclature_guid']]);
									echo $nomen && isset($nomen->id) ? $nomen->name : '';
								?>
							</td>
							<td><?php echo $item['series_name'];?></td>
							<td><?php echo $item['count'];?></td>
						</tr>
					<?php
							}
						}
					?>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 form-group">
				<?php 
					echo Html::hiddenInput("doc[guid]",$doc['guid']);
					echo Html::hiddenInput("doc[movement_type]",$doc['movement_type']);
					echo Html::hiddenInput("doc[type_of_operation]",$doc['type_of_operation']);
					echo Html::submitButton('Отменить',['name'=>'cancel','class'=>'btn btn-danger']);
					echo Html::submitButton('Подтвердить',['name'=>'commit','class'=>'btn btn-success']);
				?>
			</div>
		</div>
		<?php ActiveForm::end();?>
	</div>
</div>