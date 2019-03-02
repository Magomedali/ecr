$(function(){

	var checkFormFields = function(input){
		
		if(!input.length) return;

		if(!input.val()){
			input.removeClass("fieldIsSuccess");
			input.addClass("fieldHasError");
		}else{
			if(input.hasClass('autocomplete_required')){
				var val_input = input.siblings("input.autocomplete_input_value");
				if(val_input.val()){
					val_input.removeClass("fieldHasError");
					input.removeClass("fieldHasError");
					input.addClass("fieldIsSuccess");
				}
			}else{
				input.removeClass("fieldHasError");
				input.addClass("fieldIsSuccess");
			}
		}
	}

	$("body").on("keyup","input.isRequired",function(){
		checkFormFields($(this));
	});

	$("body").on("keypress","input.isRequired",function(){
		checkFormFields($(this));
	});

	$("body").on("change","input.isRequired",function(){
		checkFormFields($(this));
	});
})