{title url="tiki-directory_ranking.php?sort_mode=$sort_mode"}{tr}Directory ranking{/tr}{/title}

{* Display the title using parent *}
{include file='tiki-directory_bar.tpl'}
<br>
<br>
{* Navigation bar to admin, admin related, etc *}

{* Display the list of categories (items) using pagination *}
{* Links to edit, remove, browse the categories *}
<div class="table-responsive">
	<table class="table">
		<tr>
			<th><a href="tiki-directory_ranking.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></th>
			<th><a href="tiki-directory_ranking.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}URL{/tr}</a></th>
			<th><a href="tiki-directory_ranking.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'country_desc'}country_asc{else}country_desc{/if}">{tr}Country{/tr}</a></th>
			<th><a href="tiki-directory_ranking.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></th>
		</tr>

		{section name=user loop=$items}
		<tr class="{cycle advance=false}">
			<td class="text"><a class="link" href="tiki-directory_redirect.php?siteId={$items[user].siteId}" {if $prefs.directory_open_links eq 'n'}target='_blank'{/if}>{$items[user].name}</a></td>
			<td class="text">{$items[user].url}</td>
			{if $prefs.directory_country_flag eq 'y'}
				<td class="icon"><img src='img/flags/{$items[user].country}.png' alt='{$items[user].country}'></td>
			{/if}
			<td class="integer">{$items[user].hits}</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="text" colspan="4"><i>{tr}Directory Categories:{/tr}{assign var=fsfs value=1}
				{section name=ii loop=$items[user].cats}
				{if $fsfs}{assign var=fsfs value=0}{else}, {/if}
				{$items[user].cats[ii].path}
				{/section}</i>
			</td>
		</tr>
		{sectionelse}
			{norecords _colspan=4}
		{/section}
	</table>
</div>
{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
