<?php

?>
<div id="autocomplete_block-<?php echo $id ?>" class="autocomplete__widget_block form-group">
    <input type="hidden" name="<?php echo $inputValueName?>" value="<?php echo $inputValueName_Value?>" class="autocomplete_input_value" id="autocomplete_input_value-<?php echo $id ?>">
    
    <?php if($labelShow){?>
        <label class="form-label"><?php echo $label?></label>
    <?php } ?>
    
    <input type="text" name="<?php echo $inputKeyName?>" autocomplete="off" value="<?php echo $inputKeyName_Value?>" class="form-control autocomplete_input_key" id="autocomplete_input_key-<?php echo $id ?>" data-action="<?php echo $apiUrl;?>" placeholder='<?php echo $placeholder;?>'>
    
    <div class="autocomplete_data" id="autocomplete_data-<?php echo $id ?>" data-block="0">
    	<ul id="autocomplete_itemse-<?php echo $id ?>" class="autocomplete_items">
    		<?php
    			if(is_array($data) && count($data)){
    				foreach ($data as $key => $value) {
    		?>
    			<li data-value="<?php echo $key?>"> <?php echo $value?> </li>
    		<?php
    				}
    			}
    		?>
    	</ul>
    </div>
</div>