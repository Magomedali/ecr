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
			'userId'=>"wNomenclatureGuid_$count",
			'apiUrl'=>Url::to(['/autocomplete/nomenclature']),
			'inputValueName'=>"MaterialsAppItem[$count][nomenclature_guid]",
			'inputValueName_Value'=>"",
			'inputKeyName'=>"MaterialsAppItem[$count][nomenclature_name]",
			'inputKeyName_Value'=>"",
			'placeholder'=>'Номенклатура',
			'labelShow'=>false,
			'properties'=>[
				['property'=>'unit','commonElement'=>'tr','targetElement'=>'td.nomenclature_unit input'],
			],
			'generateSearchFiltersCallback'=>"function(){
				
				var ns = $('#tableMaterials').find('input[name$=\'[nomenclature_guid]\'][name^=\'MaterialsAppItem\']');

				if(ns.length){
					var data = [];
					ns.each(function(){
						data.push($(this).val());
					});

					return {
						extends:data
					}

				}else{
					return {};
				}
			}"
		]);
	?>
	</td>
	<td>
		<?php echo Html::input("number","MaterialsAppItem[{$count}][count]",null,['min'=>0,'step'=>'0.001','class'=>'form-control input-sm isRequired'])?>
	</td>
	<td class="nomenclature_unit">
		<?php echo Html::textInput("MaterialsAppItem[{$count}][nomenclature_unit]",null,['readonly'=>true,'class'=>'form-control']);?>
	</td>
	<td><?php echo html::a('-',null,['class'=>'btn btn-sm btn-danger btnRemoveRow']);?></td>
</tr>