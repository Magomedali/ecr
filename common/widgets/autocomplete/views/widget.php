<?php

use yii\helpers\Html;

?>
<div id="autocomplete_block-<?php echo $id ?>" class="autocomplete__widget_block form-group">
    
    <input type="hidden" name="<?php echo $inputValueName;?>" value="<?php echo Html::encode($inputValueName_Value);?>" class="autocomplete_input_value <?php echo $required ? 'autocomplete_required' : '' ;?>" id="autocomplete_input_value-<?php echo $id; ?>">
    
    <?php if($labelShow){?>
        <label class="form-label"><?php echo $label?></label>
    <?php } ?>
    
    <input type="hidden" name="autocomplete_properties" class='autocomplete_properties' data-properties='<?php echo json_encode($properties);?>'>

    <input type="hidden" name="autocomplete_parameters" class='autocomplete_parameters' data-parameters='<?php echo json_encode($parameters);?>'>
    <span style="display: none;" class="autocomplete_options" data-options='<?php echo json_encode($options);?>'></span>
    <span style="display: none;" class="autocomplete_widget_id" data-widget_id='<?php echo $id;?>'></span>

    <input type="text" name="<?php echo $inputKeyName;?>" autocomplete="off" value="<?php echo Html::encode($inputKeyName_Value);?>" class="form-control autocomplete_input_key <?php echo $required ? 'autocomplete_required' : '';?> input-sm" id="autocomplete_input_key-<?php echo $id; ?>" data-action="<?php echo $apiUrl;?>" placeholder='<?php echo $placeholder;?>'>

    <span class="reset_autocomplete <?php echo $labelShow ? 'withLabel' : '';?>" <?php echo $inputKeyName_Value ? '' : 'style="display:none;"'?>>X</span>

    <div class="autocomplete_data" id="autocomplete_data-<?php echo $id ?>" data-block="0">
    	<ul id="autocomplete_itemse-<?php echo $id ?>" class="form-control autocomplete_items" size="6">
    		<?php
    			if(is_array($data) && count($data)){
    				foreach ($data as $key => $value) {
    		?>
    			<li class="autocomplete_item" data-value="<?php echo $key?>"> <?php echo $value?> </li>
    		<?php
    				}
    			}
    		?>
    	</ul>
    </div>
    <script type="text/javascript">
        var WObject_<?php echo $id?> = {
            id : '<?php echo $id?>',
            enabledTabletWindow:0,
            tabletWindow:"",
            tabletWindowBtnClose:"",
            tabletWindowInputKey:"",
            tabletWindowInputKeyResetBtn:"",
            tabletWindowList:"",
            initTabletWindow:function(){
                this.tabletWindow = $("<div/>").attr("id","tabletWindow_"+'<?php echo $id?>').addClass("tabletWindow").attr("data-id","<?php echo $id?>");
                this.tabletWindowInputKey = $("<input/>").attr("text","input").attr("name","tabletWindowInputKey").addClass("tabletWindowInputKey form-control").attr("placeholder","Искать");
                this.tabletWindowInputKeyResetBtn = $("<button/>").addClass("tabletWindowInputKeyResetBtn btn btn-danger").text("X");
                this.tabletWindowList = $("<ul/>").addClass("tabletWindowList");
                this.tabletWindowBtnClose = $("<span/>").addClass("tabletWindowBtnClose").text("Закрыть");

                var divInput = $("<div/>").addClass("tabletWindowInputKeyBlock").html(this.tabletWindowInputKey).append(this.tabletWindowInputKeyResetBtn);
                var divRow1 = $("<div/>").addClass("row").html($("<div/>").addClass("col-md-12 tabletWindowHeader").html($("<h2/>").text("<?php echo $label?>")).append(this.tabletWindowBtnClose));
                var divRow2 = $("<div/>").addClass("row")
                        .html($("<div/>").addClass("col-md-12").append(divInput));

                var divRow3 = $("<div/>").addClass("row").html($("<div/>").addClass("col-md-12").html(this.tabletWindowList));

                this.tabletWindow.append(divRow1).append(divRow2).append(divRow3);
                $("body").append(this.tabletWindow);
                this.tabletWindow.css("height",document.body.scrollHeight+"px");
                this.enabledTabletWindow = 1;
            },
            onSelectCallback : <?php echo $onSelectCallback;?>,
            generateSearchFiltersCallback: <?php echo $generateSearchFiltersCallback; ?>
        }

        $(function(){
            if(window.matchMedia('(max-width: 1024px)').matches){
                WObject_<?php echo $id?>.initTabletWindow();
            }
        })
    </script>
</div>