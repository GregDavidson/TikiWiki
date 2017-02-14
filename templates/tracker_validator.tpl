{* $Id: tracker_validator.tpl 59908 2016-10-04 21:30:00Z luciash $ *}
{if isset($validationjs)}{jq}
$("#editItemForm{{$trackerEditFormId}}").validate({
	{{$validationjs}},
	submitHandler: function(){
		if( typeof nosubmitItemForm{{$trackerEditFormId}} !== "undefined" && nosubmitItemForm{{$trackerEditFormId}} == true ) {
			return false;
		} else {
			return process_submit(this.currentForm);
		}
	}
});
{/jq}{/if}
