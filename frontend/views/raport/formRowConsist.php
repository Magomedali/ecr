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
			'apiUrl'=>Url::to(['/autocomplete/users']),
			'inputValueName'=>"RaportConsist[$count][user_guid]",
			'inputValueName_Value'=>"",
			'inputKeyName'=>'master_key',
			'inputKeyName_Value'=>"",
			'placeholder'=>'Укажите физ.лицо',
			'labelShow'=>false,
			'properties'=>[
				['property'=>'ktu','commonElement'=>'tr','targetElement'=>'td.person_ktu']
			]
		]);
	?>
	</td>
	<td>
	<?php 
		echo AutoComplete::widget([
			'data'=>[],
			'apiUrl'=>Url::to(['/autocomplete/technics']),
			'inputValueName'=>"RaportConsist[$count][technic_guid]",
			'inputValueName_Value'=>"",
			'inputKeyName'=>'master_key',
			'inputKeyName_Value'=>"",
			'placeholder'=>'Укажите технику',
			'labelShow'=>false
		]);
	?>	
	</td>
	<td class="person_ktu"></td>
	<td><?php echo html::a('-',null,['class'=>'btn btn-sm btn-danger btnRemoveRow']);?></td>
</tr>