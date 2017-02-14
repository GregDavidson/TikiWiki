{* $Id: file_backlinks.tpl 50960 2014-04-24 15:27:33Z lphuberdeau $ *}
<ul>
	{foreach from=$backlinks item=object}
		<li><a href="{$object.itemId|sefurl:$object.type}">{$object.name|escape}</a></li>
	{/foreach}
</ul>
