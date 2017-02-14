{* $Id: wikiplugin_subscribegroup.tpl 48719 2013-11-24 03:07:07Z chibaguy $ *}
{strip}
<form method="post">
<input type="hidden" name="group" value="{$subscribeGroup|escape}">
<input type="hidden" name="iSubscribeGroup" value="{$iSubscribeGroup}">
{$text|escape}
<div><input type="submit" class="btn btn-default btn-sm" name="subscribeGroup" value="{tr}{$action}{/tr}"></div>
</form>
{/strip}