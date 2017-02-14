{* $Id: wikiplugin_adjustinventory.tpl 53176 2014-11-21 23:12:16Z jyhem $ *}
<form method="post" action="{query _type=relative _keepall=y}" style="display: inline;">
	<input type="hidden" name="code" value="{$code|escape}">
	{if $add == 'y'}
		{tr}Add:{/tr}
		<input type="text" name="add_quantity" value="0" size="4">
		<br>
	{/if}
	{if $subtract == 'y'}
		{tr}Subtract:{/tr}
		<input type="text" name="subtract_quantity" value="0" size="4">
		<br>
	{/if}
	<input type="submit" class="btn btn-default btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
</form>

