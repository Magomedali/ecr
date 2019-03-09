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
			'userId'=>"wUserId_rcu_$count",
			'apiUrl'=>Url::to(['/autocomplete/users']),
			'inputValueName'=>"RaportConsist[$count][user_guid]",
			'inputValueName_Value'=>"",
			'inputKeyName'=>"RaportConsist[$count][user_name]",
			'inputKeyName_Value'=>"",
			'placeholder'=>'Укажите физ.лицо',
			'label'=>'Физ. лицо',
			'labelShow'=>false,
			'properties'=>[
				['property'=>'ktu','commonElement'=>'tr','targetElement'=>'td.person_ktu span'],

				['property'=>'ktu','commonElement'=>'tr','targetElement'=>'td.person_ktu input.hidden_user_ktu'],
				['property'=>'exists_technic','commonElement'=>'tr','targetElement'=>'td.td_technic input.mock_object'],
				//['property'=>'technic_name','commonElement'=>'tr','targetElement'=>'td.td_technic input.autocomplete_input_key']
			],
			'generateSearchFiltersCallback'=>"function(){
				
				var users = $('#tableConsist').find('input[name$=\'[user_guid]\'][name^=\'RaportConsist\']');

				if(users.length){
					var data = [];
					users.each(function(){
						data.push($(this).val());
					});

					return {
						users_extends:data
					}

				}else{
					return {};
				}
			}",
			'onSelectCallback'=>"function(item){
				if(!item.length) return;
				var exists_technic = item.attr('data-exists_technic');
				var commonEl = item.parents('tr');
				var InputElements = commonEl.find('td.td_technic input.autocomplete_input_key,td.td_technic input.autocomplete_input_value');

				if(exists_technic !== 'true'){
					InputElements.removeClass('autocomplete_required');
					InputElements.removeClass('fieldHasError');
					InputElements.addClass('fieldIsSuccess');
					InputElements.val(null);
				}else{
					InputElements.addClass('autocomplete_required');
				}											
			}"
		]);
	?>
	</td>
	<td class="td_technic">
	<?php 
		echo AutoComplete::widget([
			'data'=>[],
			'userId'=>"wUserId_rct_$count",
			'apiUrl'=>Url::to(['/autocomplete/technics']),
			'inputValueName'=>"RaportConsist[$count][technic_guid]",
			'inputValueName_Value'=>"",
			'inputKeyName'=>"RaportConsist[$count][technic_name]",
			'inputKeyName_Value'=>"",
			'placeholder'=>'Укажите технику',
			'labelShow'=>false,
			'label'=>'Техника'
		]);
	?>	
	</td>
	<td class="person_ktu">
		<span></span>
		<?php echo Html::hiddenInput("RaportConsist[{$count}][user_ktu]",null,['class'=>'hidden_user_ktu'])?>
	</td>
	<td><?php echo html::a('-',null,['class'=>'btn btn-sm btn-danger btnRemoveRow']);?></td>
</tr>