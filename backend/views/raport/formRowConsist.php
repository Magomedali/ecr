<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\widgets\autocomplete\AutoComplete;
?>

<tr>
	<td>
	<?php 
		echo AutoComplete::widget([
			'data'=>[],
			'apiUrl'=>Url::to(['/autocomplete/users']),
			'inputValueName'=>"RaportConsist[$count][user_guid]",
			'inputValueName_Value'=>"",
			'inputKeyName'=>"RaportConsist[$count][user_name]",
			'inputKeyName_Value'=>"",
			'placeholder'=>'Укажите физ.лицо',
			'labelShow'=>false,
			'properties'=>[
				['property'=>'ktu','commonElement'=>'tr','targetElement'=>'td.person_ktu span'],

				['property'=>'ktu','commonElement'=>'tr','targetElement'=>'td.person_ktu input.hidden_user_ktu'],
				//['property'=>'technic_guid','commonElement'=>'tr','targetElement'=>'td.td_technic input.autocomplete_input_value'],
				//['property'=>'technic_name','commonElement'=>'tr','targetElement'=>'td.td_technic input.autocomplete_input_key']
			]
		]);
	?>
	</td>
	<td class="td_technic">
	<?php 
		echo AutoComplete::widget([
			'data'=>[],
			'apiUrl'=>Url::to(['/autocomplete/technics']),
			'inputValueName'=>"RaportConsist[$count][technic_guid]",
			'inputValueName_Value'=>"",
			'inputKeyName'=>"RaportConsist[$count][technic_name]",
			'inputKeyName_Value'=>"",
			'placeholder'=>'Укажите технику',
			'labelShow'=>false
		]);
	?>	
	</td>
	<td class="person_ktu">
		<span></span>
		<?php echo Html::hiddenInput("RaportConsist[{$count}][user_ktu]",null,['class'=>'hidden_user_ktu'])?>
	</td>
	<td><?php echo html::a('-',null,['class'=>'btn btn-sm btn-danger btnRemoveRow']);?></td>
</tr>