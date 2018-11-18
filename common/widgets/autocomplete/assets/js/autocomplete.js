$(function(){

    var sendRequestAutocomplete = 0;

    var searchEntityByKey = function(elem){

        if(!elem.length) return;

        var thisElement = elem;
        var val = thisElement.val();

        var options = thisElement.siblings(".autocomplete_options").data("options");
        
        if(options.hasOwnProperty("minKeyLength")){
            if(val.length < options.minKeyLength) return;
        }

        var properties = thisElement.siblings("input.autocomplete_properties").data("properties");
        var parameters = thisElement.siblings("input.autocomplete_parameters").data("parameters");
        var autocomplete_items = thisElement.siblings(".autocomplete_data").find(".autocomplete_items");
        var action = thisElement.data("action");
        
        var data = {};
        data['key']=val;

        if(parameters.length){
            $.each(parameters,function(i,p){
                if(p.hasOwnProperty("valueFromElement") && p.hasOwnProperty("name")){
                    var vfe = $(p.valueFromElement);
                    if(vfe.length){

                        if(vfe.prop("nodeName") == "INPUT" || vfe.prop("nodeName") == "SELECT"){
                            data[p.name]=vfe.val();
                        }else{
                            data[p.name]=vfe.text();
                        }
                    }                           
                }
            })
        }

        if(!sendRequestAutocomplete && action && autocomplete_items.length){

            $.ajax({
                url:action,
                type:"GET",
                data:data,
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

                                if(item.hasOwnProperty("title") && item.hasOwnProperty("value")){
                                    var li = "<li data-value='"+item.value+"'";
                                    
                                    //Additional properties
                                    if(properties.length){
                                        $.each(properties,function(i,p){
                                            if(p.hasOwnProperty("commonElement") && p.hasOwnProperty("property") && p.hasOwnProperty("targetElement")){
                                                
                                                if(item.hasOwnProperty(p.property)){
                                                     li+= " data-"+p.property+"='"+item[p.property]+"'";
                                                }
                                            }
                                        })
                                    }

                                    li += ">"+item.title + "</li>";
                                    html += li;
                                }
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
    };

    $("body").on("keyup",".autocomplete__widget_block .autocomplete_input_key",function(){
        searchEntityByKey($(this));
    });
    

    $("body").on("focusin",".autocomplete__widget_block .autocomplete_input_key",function(){
        $(this).siblings(".autocomplete_data").show(100);

        var options = $(this).siblings(".autocomplete_options").data("options");
        
        if(options.hasOwnProperty("searchOnFocusin") && options.searchOnFocusin){
            searchEntityByKey($(this));
        }

    });


    $("body").on("focusout",".autocomplete__widget_block .autocomplete_input_key",function(){
        var block_data = $(this).siblings(".autocomplete_data");
        if(block_data.length && !parseInt(block_data.attr("data-block")))
            block_data.hide(100);
    });

    $("body").on("mouseover",".autocomplete__widget_block .autocomplete_items",function(){
        $(this).parents(".autocomplete_data").attr("data-block",1);
    });

    $("body").on("mouseout",".autocomplete__widget_block .autocomplete_items",function(){
        $(this).parents(".autocomplete_data").attr("data-block",0);
    });

    
    $("body").on("click",".autocomplete__widget_block .autocomplete_items li",function(){

    	var value = $(this).attr("data-value");
    		
        var inputValue = $(this).parents(".autocomplete__widget_block").find("input.autocomplete_input_value");
        var inputKey = $(this).parents(".autocomplete__widget_block").find("input.autocomplete_input_key")
            
        inputValue.val(value);
    	inputKey.val($(this).text());

        var data = $(this).data();
        var properties = inputKey.siblings("input.autocomplete_properties").data("properties");
        //additional properties set
        if(properties.length){
            $.each(properties,function(i,p){
                if(p.hasOwnProperty("commonElement") && p.hasOwnProperty("property") && p.hasOwnProperty("targetElement")){
                    
                    var tEl = inputValue.parents(p.commonElement).find(p.targetElement);
                                    
                    if(tEl.length && data.hasOwnProperty(p.property)){
                        if(tEl.prop("nodeName") == "INPUT" || tEl.prop("nodeName") == "SELECT"){
                            tEl.val(data[p.property]);
                        }else{
                            tEl.html(data[p.property]);
                        }
                    }
                }
            })
        }

    	$(this).parents(".autocomplete_data").attr("data-block",0);
    	$(this).parents(".autocomplete_data").hide(100);
    });

    					
})