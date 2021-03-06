<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\widgets\autocomplete\AutoComplete;
use common\models\RaportWork;

?>
<tr data-order="<?php echo $count?>">
	<td>
	<?php 

		echo AutoComplete::widget([
			'data'=>[],
			'userId'=>"wUserId_rww_$count",
			'apiUrl'=>Url::to(['/autocomplete/works']),
			'inputValueName'=>"RaportWork[$count][work_guid]",
			'inputValueName_Value'=>"",
			'inputKeyName'=>"RaportWork[$count][work_name]",
			'inputKeyName_Value'=>"",
			'placeholder'=>'Укажите вид работы',
			'labelShow'=>false,
			'label'=>'Вид работы',
			'properties'=>[
				['property'=>'work_nomenclatures','commonElement'=>'td','targetElement'=>'.work_assigned_nomencaltures'],
				['property'=>'req_percent_save','commonElement'=>'tr','targetElement'=>'td.td_percent_save input[type=hidden].req_percent_save']
			],
			'onSelectCallback'=>"function(item){
				if(!item.length) return;
				var commonEl = item.parents('tr');
				var req_percent_save = parseInt(item.attr('data-req_percent_save'));
				var selectElement = commonEl.find('td.td_percent_save select');
				selectElement.attr('disabled',!req_percent_save);
																
				if(!req_percent_save){
					selectElement.removeClass('isRequired');
					selectElement.removeClass('fieldHasError');
					selectElement.val('1').trigger('change');
				}else{
					selectElement.addClass('isRequired').addClass('fieldHasError');
				}
			}",
			'generateSearchFiltersCallback'=>"function(){
				return {
					extends:{
						is_regulatory:0
					}
				}
			}"
		]);
	?>
	<?php
		echo Html::hiddenInput("RaportWork[$count][work_nomenclatures]",null,['class'=>'work_assigned_nomencaltures']);
	?>
	</td>
	<td class="td_line_guid">
	<?php 
		echo AutoComplete::widget([
			'data'=>[],
			'userId'=>"wUserId_rwl_$count",
			'apiUrl'=>Url::to(['/autocomplete/lines']),
			'inputValueName'=>"RaportWork[$count][line_guid]",
			'inputValueName_Value'=>"",
			'inputKeyName'=>"RaportWork[$count][line_name]",
			'inputKeyName_Value'=>"",
			'placeholder'=>'Укажите линию',
			'labelShow'=>false,
			'label'=>'Линия',
			'properties'=>[
				['property'=>'hint_length','commonElement'=>'tr','targetElement'=>'td.td_length span.hint_length,td.td_length input[type=hidden].hint_length'],
				['property'=>'hint_count','commonElement'=>'tr','targetElement'=>'td.td_count span.hint_count,td.td_count input[type=hidden].hint_count'],
				['property'=>'is_countable','commonElement'=>'tr','targetElement'=>'td.td_count input[type=hidden].is_countable'],
			],
			'onSelectCallback'=>"function(item){
																if(!item.length) return;
																var is_countable = parseInt(item.attr('data-is_countable'));
																var commonEl = item.parents('tr');
																var InputElement = commonEl.find('td.td_count input[type=number]');
																InputElement.attr('readonly',!is_countable);
																

																if(!is_countable){
																	commonEl.find('td.td_count input[type=hidden].hint_count').val(null);
																	commonEl.find('td.td_count span.hint_count').html(null);
																	InputElement.removeClass('isRequired');
																	InputElement.removeClass('fieldHasError');
																	InputElement.val(null);
																}else{
																	InputElement.addClass('isRequired');
																}
																
															}"
		]);
	?>	
	</td>
	<td>
		<?php echo Html::checkbox("RaportWork[$count][mechanized]",null); ?>
	</td>
	<td class="td_length">
		<?php echo Html::input("number","RaportWork[$count][length]",null,['class'=>'form-control input-sm isRequired','step'=>"0.01",'autocomplete'=>'off']); ?>
		<?php echo Html::hiddenInput("RaportWork[$count][hint_length]",null,['class'=>'hint_length'])?>
		<span class="hint_field hint_length"></span>
	</td>
	<td class="td_count">
		<?php echo Html::input("number","RaportWork[$count][count]",null,['class'=>'form-control input-sm isRequired','step'=>"0.01",'autocomplete'=>'off']); ?>
		<?php echo Html::hiddenInput("RaportWork[$count][hint_count]",null,['class'=>'hint_count'])?>
		<?php echo Html::hiddenInput("RaportWork[$count][is_countable]",null,['class'=>'is_countable'])?>
		<span class="hint_field hint_count"></span>
	</td>
	<td class="td_percent_save">
		<?php 
			echo Html::dropDownList("RaportWork[$count][percent_save]",null,
															RaportWork::getPercents(),
															[
																'class'=>'form-control input-sm',
																'disabled'=>true
															]
				); 
		?>

		<?php echo Html::hiddenInput("RaportWork[$count][req_percent_save]",null,['class'=>'req_percent_save'])?>
	</td>
	<td class="td_squaremeter">
		<?php echo Html::textInput("RaportWork[$count][squaremeter]",null,['class'=>'form-control input-sm','readonly'=>1]); ?>
	</td>
	<td><?php echo html::a('-',null,['class'=>'btn btn-sm btn-danger btnRemoveRow']);?></td>
</tr>