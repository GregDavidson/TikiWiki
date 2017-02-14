{title help="Referer Stats"}{tr}Referer stats{/tr}{/title}

<div class="t_navbar">
	{button href="tiki-referer_stats.php?clear=1" class="btn btn-default" _text="{tr}Clear Stats{/tr}"}
</div>

{include file='find.tpl'}

<div class="table-responsive">
	<table class="table">
		<tr>
			<th>
				<a href="tiki-referer_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'referer_desc'}referer_asc{else}referer_desc{/if}">{tr}Domain{/tr}</a>
			</th>
			<th>
				<a href="tiki-referer_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a>
			</th>
			<th>
				<a href="tiki-referer_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'last_desc'}last_asc{else}last_desc{/if}">{tr}Last{/tr}</a>
			</th>
		</tr>

		{section name=user loop=$channels}
			<tr>
				<td class="text"><a href="{$channels[user].lasturl}" target="_blank">{$channels[user].referer}</a></td>
				<td class="integer">{$channels[user].hits}</td>
				<td class="date">{$channels[user].last|tiki_short_datetime}</td>
			</tr>
		{sectionelse}
			{norecords _colspan=3}
		{/section}
	</table>
</div>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
