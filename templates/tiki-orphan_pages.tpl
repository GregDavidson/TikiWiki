{* $Id: tiki-orphan_pages.tpl 57978 2016-03-18 12:03:53Z jonnybradley $ *}

{title admpage="wiki" help="Using Wiki Pages#Backlinks_amp_Orphan_Pages"}{tr}Orphan Pages{/tr}{/title}

{if $listpages or ($find ne '')}
	{include file='find.tpl' find_show_languages='y' find_show_categories='y' find_show_num_rows='y'}
{/if}

<div id="tiki-listpages-content">
	{include file='tiki-listpages_content.tpl'}
</div>

