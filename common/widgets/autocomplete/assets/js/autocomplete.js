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
        
        //Удаляем ранее установленные значения параметров
        if(val.length == 0){
            var inputValue = thisElement.siblings("input.autocomplete_input_value");
            inputValue.val(null).trigger('change');
            if(properties.length){
                $.each(properties,function(i,p){
                    if(p.hasOwnProperty("targetElement") && p.hasOwnProperty("commonElement")){            
                        var pr =  thisElement.parents(p.commonElement).find(p.targetElement);
                        pr.length
                        if(pr.length){
                            pr.each(function(){
                                if($(this).prop("nodeName") == "INPUT" || $(this).prop("nodeName") == "SELECT"){
                                    $(this).val(null).trigger('change');
                                }else{
                                    $(this).html(null);
                                }
                            })
                        }
                    }
                })
            }
        }

        var data = {};

        var wId = thisElement.siblings("span.autocomplete_widget_id").data("widget_id");
        var widgetObject = "WObject_"+wId;
        var tabletList = null;
        if(window.hasOwnProperty(widgetObject)){
            var WObject = window[widgetObject];
            if(WObject.hasOwnProperty('generateSearchFiltersCallback'))
                data = WObject.generateSearchFiltersCallback($(this));

            if(WObject.hasOwnProperty("tabletWindowList") && WObject.tabletWindowList.length){
                tabletList = WObject.tabletWindowList;
            }
        }

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
                                    var opt = "<li class='autocomplete_item' data-value='"+item.value+"'";
                                    //Additional properties
                                    if(properties.length){
                                        $.each(properties,function(i,p){
                                            if(p.hasOwnProperty("commonElement") && p.hasOwnProperty("property") && p.hasOwnProperty("targetElement")){
                                                if(item.hasOwnProperty(p.property)){
                                                     opt+= " data-"+p.property+"='"+item[p.property]+"'";
                                                }
                                            }
                                        })
                                    }
                                    opt += ">"+item.title + "</li>";
                                    html += opt;
                                }
                            });
                            autocomplete_items.html(html);
                            if(tabletList){
                                tabletList.html(html);
                            }
                            
                        }else{
                            autocomplete_items.html("");
                            if(tabletList){
                                tabletList.html("");
                            }
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


    var checkFormAutocompleteFields = function(input){
        
        if(!input.length) return;

        if(!input.val()){
            input.removeClass("fieldIsSuccess");
            input.addClass("fieldHasError");
        }else{
            var val_input = input.siblings("input.autocomplete_input_value");
            if(val_input.val()){
                val_input.removeClass("fieldHasError");
                input.removeClass("fieldHasError");
                input.addClass("fieldIsSuccess");
            }
        }
    };

    var showResetBtn = function(input){
        if(!input.length) return;
        if(!input.val()){
            input.siblings("span.reset_autocomplete").hide();
        }else{
            input.siblings("span.reset_autocomplete").show();
        }
    };
    
    $("body").on("keyup","input.autocomplete_required",function(){
        checkFormAutocompleteFields($(this));
    });
    $("body").on("keypress","input.autocomplete_required",function(){
        checkFormAutocompleteFields($(this));
    });
    $("body").on("change","input.autocomplete_required",function(){
        checkFormAutocompleteFields($(this));
    });

    $("body").on("keyup",".autocomplete__widget_block .autocomplete_input_key",function(){
        searchEntityByKey($(this));
        showResetBtn($(this));
    });
    
    $("body").on("keypress","input.autocomplete_input_key",function(){
        showResetBtn($(this));
    });
    $("body").on("change","input.autocomplete_input_key",function(){
        showResetBtn($(this));
    });

    $("body").on("focusin",".autocomplete__widget_block .autocomplete_input_key",function(){
        
        var wId = $(this).siblings("span.autocomplete_widget_id").data("widget_id");
        var widgetObject = "WObject_"+wId;
        var tabletWindowOpened = false;
        if(window.hasOwnProperty(widgetObject)){
            var WObject = window[widgetObject];
            if(WObject.hasOwnProperty('enabledTabletWindow') && WObject.enabledTabletWindow){
                WObject.tabletWindowInputKey.val($(this).val()).trigger("focusin");
                WObject.tabletWindow.show();
                tabletWindowOpened = true;
            }
        }

        if(!tabletWindowOpened){
            $(this).siblings(".autocomplete_data").show(100);

            var options = $(this).siblings(".autocomplete_options").data("options");
            
            if(options.hasOwnProperty("searchOnFocusin") && options.searchOnFocusin){
                searchEntityByKey($(this));
            }
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

    
    $("body").on("click",".autocomplete__widget_block .autocomplete_items .autocomplete_item",function(){

    	var value = $(this).attr("data-value");
    		
        var inputValue = $(this).parents(".autocomplete__widget_block").find("input.autocomplete_input_value");
        var inputKey = $(this).parents(".autocomplete__widget_block").find("input.autocomplete_input_key")
            
        inputValue.val(value).trigger('change');
    	inputKey.val($(this).text()).trigger('change');

        var data = $(this).data();
        var properties = inputKey.siblings("input.autocomplete_properties").data("properties");
        //additional properties set
        if(properties.length){
            $.each(properties,function(i,p){
                if(p.hasOwnProperty("commonElement") && p.hasOwnProperty("property") && p.hasOwnProperty("targetElement")){
                    
                    var tEl = inputValue.parents(p.commonElement).find(p.targetElement);
                                    
                    if(tEl.length && data.hasOwnProperty(p.property)){
                        tEl.each(function(){
                            if($(this).prop("nodeName") == "INPUT" || $(this).prop("nodeName") == "SELECT"){
                                $(this).val(data[p.property]).trigger('change');
                            }else{
                                $(this).html(data[p.property]);
                            }
                        });
                    }
                }
            })
        }

        var wId = inputKey.siblings("span.autocomplete_widget_id").data("widget_id");
        var widgetObject = "WObject_"+wId;
        if(window.hasOwnProperty(widgetObject)){
            var WObject = window[widgetObject];
            if(WObject.hasOwnProperty('onSelectCallback'))
                WObject.onSelectCallback($(this));
        }

    	$(this).parents(".autocomplete_data").attr("data-block",0);
    	$(this).parents(".autocomplete_data").hide(100);
    });



    $("body").on("click","span.reset_autocomplete",function(){
        var input_key = $(this).siblings("input.autocomplete_input_key");
        var input_value = $(this).siblings("input.autocomplete_input_value");
        input_value.val(null).trigger("change");
        input_key.val(null).trigger("change");
    });
    


    $("body").on("click",".tabletWindowBtnClose",function(){
        $(this).parents(".tabletWindow").hide();
    });

    $("body").on("focusin",".tabletWindowInputKey",function(){
        var id = $(this).parents(".tabletWindow").attr("data-id");
        if(!id) return;
        var widget = $("#autocomplete_block-"+id);
        if(!widget.length) return;

        var inputKey = widget.find(".autocomplete_input_key");
        if(!inputKey.length) return;
        var options = inputKey.siblings(".autocomplete_options").data("options");
            
        if(options.hasOwnProperty("searchOnFocusin") && options.searchOnFocusin){
            searchEntityByKey(inputKey);
        }
    });

    $("body").on("keyup","input.tabletWindowInputKey",function(){
        var id = $(this).parents(".tabletWindow").attr("data-id");
        if(!id) return;
        var widget = $("#autocomplete_block-"+id);
        if(!widget.length) return;
        var val = $(this).val();
        widget.find(".autocomplete_input_key").val(val).trigger("keyup");
    });

    $("body").on("click",".tabletWindowList .autocomplete_item",function(){
        var id = $(this).parents(".tabletWindow").attr("data-id");
        if(!id) return;
        var widget = $("#autocomplete_block-"+id);
        if(!widget.length) return;
        var number = $(this).index();
        widget.find(".autocomplete_data .autocomplete_items .autocomplete_item").eq(number).trigger("click");
        $(this).parents(".tabletWindow").hide();
    });


    $("body").on("click",".tabletWindow .tabletWindowInputKeyResetBtn",function(){
        var id = $(this).parents(".tabletWindow").attr("data-id");
        if(!id) return;
        var widget = $("#autocomplete_block-"+id);
        if(!widget.length) return;
        $(this).parents(".tabletWindow").find(".tabletWindowInputKey").val("").trigger("keyup");
    });
})