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
			'userId'=>"wUserId_rwu_$count",
			'apiUrl'=>Url::to(['/autocomplete/users']),
			'inputValueName'=>"RaportRegulatoryWork[$count][user_guid]",
			'inputValueName_Value'=>"",
			'inputKeyName'=>"RaportRegulatoryWork[$count][user_name]",
			'inputKeyName_Value'=>"",
			'placeholder'=>'Укажите физ. лицо',
			'labelShow'=>false,
			'label'=>'Физ. лицо',
		]);
	?>
	</td>
	<td class="td_work_guid">
	<?php 
		echo AutoComplete::widget([
			'data'=>[],
			'userId'=>"wUserId_rww_$count",
			'apiUrl'=>Url::to(['/autocomplete/works']),
			'inputValueName'=>"RaportRegulatoryWork[$count][work_guid]",
			'inputValueName_Value'=>"",
			'inputKeyName'=>"RaportRegulatoryWork[$count][work_name]",
			'inputKeyName_Value'=>"",
			'placeholder'=>'Укажите вид работы',
			'label'=>'Вид работы',
			'labelShow'=>false,
			'generateSearchFiltersCallback'=>"function(){
				return {
					extends:{
						is_regulatory:1
					}
				}
			}"
		]);
	?>	
	</td>
	<td class="td_hours">
		<?php echo Html::input("number","RaportRegulatoryWork[$count][hours]",null,['class'=>'form-control input-sm isRequired','step'=>"0.01",'autocomplete'=>'off']); ?>
	</td>
	<td><?php echo html::a('-',null,['class'=>'btn btn-sm btn-danger btnRemoveRow']);?></td>
</tr>