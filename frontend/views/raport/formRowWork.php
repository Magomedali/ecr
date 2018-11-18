<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\widgets\autocomplete\AutoComplete;
?>
<tr>
	<td><?php echo $count+1; ?></td>
	<td>
	<?php 

		echo AutoComplete::widget([
			'data'=>[],
			'apiUrl'=>Url::to(['/autocomplete/works']),
			'inputValueName'=>"RaportWork[$count][work_guid]",
			'inputValueName_Value'=>"",
			'inputKeyName'=>'work_key',
			'inputKeyName_Value'=>"",
			'placeholder'=>'Укажите вид работы',
			'labelShow'=>false
		]);
	?>
	</td>
	<td>
	<?php 
		echo AutoComplete::widget([
			'data'=>[],
			'apiUrl'=>Url::to(['/autocomplete/lines']),
			'inputValueName'=>"RaportWork[$count][line_guid]",
			'inputValueName_Value'=>"",
			'inputKeyName'=>'line_key',
			'inputKeyName_Value'=>"",
			'placeholder'=>'Укажите линию',
			'labelShow'=>false
		]);
	?>	
	</td>
	<td><?php echo Html::checkbox("RaportWork[$count][mechanized]",null); ?></td>
	<td><?php echo Html::input("number","RaportWork[$count][length]",null,['class'=>'form-control isRequired','step'=>"0.01"]); ?></td>
	<td><?php echo Html::input("number","RaportWork[$count][count]",null,['class'=>'form-control isRequired','step'=>"0.01"]); ?></td>
	<td><?php echo Html::textInput("RaportWork[$count][squaremeter]",null,['class'=>'form-control','readonly'=>1]); ?></td>
	<td><?php echo html::a('-',null,['class'=>'btn btn-sm btn-danger btnRemoveRow']);?></td>
</tr>