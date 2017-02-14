{* $Id: wikiplugin_grouplist.tpl 52249 2014-08-05 20:25:58Z nkoth $ *}
{if empty($groups)}
	&mdash;
{else}
	<ul>
	{foreach from=$groups item=group}
		<li>
		{if $params.linkhome eq 'y' && !empty($group.groupHome)}
			<a href="{$group.groupHome|sefurl:wiki}">
			{assign var=link value='y'}
		{/if}
		{$group.groupName|escape}
		{if !empty($link)}
			</a>
		{/if}
		</li>
	{/foreach}
	</ul>
{/if}
