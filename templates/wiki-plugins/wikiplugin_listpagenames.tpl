{* $Id: wikiplugin_listpagenames.tpl 55973 2015-08-02 15:50:25Z jonnybradley $ *}
{strip}
<ul>
	{section name=ix loop=$listpages}
		<li>
			<a href="{$listpages[ix].pageName|sefurl}" class="link" title="{tr}view{/tr}">
				{if !empty($showPageAlias) and $showPageAlias eq 'y' and !empty($listpages[ix].page_alias)}
					{$listpages[ix].page_alias}
				{else}
					{$listpages[ix].pageName}
				{/if}
			</a>
		</li>
	{/section}
	{if $showNumberOfPages eq 'y'}
		{tr}Number of result:{/tr}{$listpages|@count}
	{/if}
</ul>
{/strip}
