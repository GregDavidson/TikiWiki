{strip}
{* $Id: wikiplugin_trackerprefill.tpl 48719 2013-11-24 03:07:07Z chibaguy $ *}
<form action="tiki-index.php?page={$params.page|escape:url}{$params.urlparams}" method="post">
	{capture name=prefills}
	{foreach from=$prefills item=field name=foo}
		{if !$smarty.foreach.foo.first}:{/if}
		{$field.fieldId}
	{/foreach}
	{/capture}
	<input type="hidden" name="prefills" value="{$smarty.capture.prefills}">
	{foreach from=$prefills item=field}
		<input type="hidden" name="values[]" value="{$field.value|escape}">
	{/foreach}
	<input type="submit" class="btn btn-default btn-sm button submit" name="go" value="{if $params.label}{tr}{$params.label}{/tr}{else}{tr}Go{/tr}{/if}">
</form>
{/strip}
