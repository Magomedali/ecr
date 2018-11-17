$(function(){

    $("body").on("focusin",".autocomplete__widget_block .autocomplete_input_key",function(){
        $(this).siblings(".autocomplete_data").show(100);
    });


    $("body").on("focusout",".autocomplete__widget_block .autocomplete_input_key",function(){
        var block_data = $(this).siblings(".autocomplete_data");
    	if(block_data.length && !parseInt(block_data.attr("data-block")))
    		block_data.hide(100);
    });


    var sendRequestAutocomplete = 0;
    $("body").on("keyup",".autocomplete__widget_block .autocomplete_input_key",function(){
    	var val = $(this).val();


    	if(val.length < 2) return;
        var autocomplete_items = $(this).siblings(".autocomplete_data").find(".autocomplete_items");
    	var action = $(this).data("action");


        if(!sendRequestAutocomplete && action && autocomplete_items.length){

    	    $.ajax({
    		    url:action,
    		    type:"GET",
    		    data:{key:val},
    		    dataType:"json",
    			before:function(){
                    sendRequestAutocomplete = 1;
                },
    			success:function(json){
    				if(json.hasOwnProperty("data")){
    					var data = json.data;
    					if(data.length){
    						var html = "";

    						$.each(data,function(i,item){
                                if(item.hasOwnProperty("title") && item.hasOwnProperty("value"))
    							html += "<li data-value='"+item.value+"'>" + item.title + "</li>";
    						})

    						autocomplete_items.html(html);
    					}else{
                            autocomplete_items.html("");
                        }
    				}

    			},
    			error:function(e){
    				console.log(e);
    			},
    			complete:function(){
                    sendRequestAutocomplete = 0;
                }
    		});
        }
    });
        

    $("body").on("mouseover",".autocomplete__widget_block .autocomplete_items",function(){
        $(this).parents(".autocomplete_data").attr("data-block",1);
    });

    $("body").on("mouseout",".autocomplete__widget_block .autocomplete_items",function(){
        $(this).parents(".autocomplete_data").attr("data-block",0);
    });

    
    $("body").on("click",".autocomplete__widget_block .autocomplete_items li",function(){

        console.log("click");
    	var value = $(this).attr("data-value");
    		
        var inputValue = $(this).parents(".autocomplete__widget_block").find("input.autocomplete_input_value");
        var inputKey = $(this).parents(".autocomplete__widget_block").find("input.autocomplete_input_key")
            
        inputValue.val(value);
    	inputKey.val($(this).text());

    	$(this).parents(".autocomplete_data").attr("data-block",0);
    	$(this).parents(".autocomplete_data").hide(100);
    });

    					
})