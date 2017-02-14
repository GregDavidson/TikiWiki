{* $Id: tiki-lastchanges.tpl 57978 2016-03-18 12:03:53Z jonnybradley $ *}

{title admpage="wiki" help="Using Wiki Pages#Last_Changes" url="tiki-lastchanges.php?days=$days"}{tr}Last Changes{/tr}{/title}

<div class="t_navbar btn-group margin-bottom-md">
	{if $days eq '1'}{assign var=thisclass value='active'}{else}{assign var=thisclass value=''}{/if}
	{button href="tiki-lastchanges.php?days=1" class="btn btn-default" _text="{tr}Today{/tr}" _class=$thisclass}
	{if $days eq '2'}{assign var=thisclass value='active'}{else}{assign var=thisclass value=''}{/if}
	{button href="tiki-lastchanges.php?days=2" class="btn btn-default" _text="{tr}Last 2 days{/tr}" _class=$thisclass}
	{if $days eq '3'}{assign var=thisclass value='active'}{else}{assign var=thisclass value=''}{/if}
	{button href="tiki-lastchanges.php?days=3" class="btn btn-default" _text="{tr}Last 3 days{/tr}" _class=$thisclass}
	{if $days eq '5'}{assign var=thisclass value='active'}{else}{assign var=thisclass value=''}{/if}
	{button href="tiki-lastchanges.php?days=5" class="btn btn-default" _text="{tr}Last 5 days{/tr}" _class=$thisclass}
	{if $days eq '7'}{assign var=thisclass value='active'}{else}{assign var=thisclass value=''}{/if}
	{button href="tiki-lastchanges.php?days=7" class="btn btn-default" _text="{tr}Last week{/tr}" _class=$thisclass}
	{if $days eq '14'}{assign var=thisclass value='active'}{else}{assign var=thisclass value=''}{/if}
	{button href="tiki-lastchanges.php?days=14" class="btn btn-default" _text="{tr}Last 2 weeks{/tr}" _class=$thisclass}
	{if $days eq '31'}{assign var=thisclass value='active'}{else}{assign var=thisclass value=''}{/if}
	{button href="tiki-lastchanges.php?days=31" class="btn btn-default" _text="{tr}Last month{/tr}" _class=$thisclass}
	{if $days eq '0'}{assign var=thisclass value='active'}{else}{assign var=thisclass value=''}{/if}
	{button href="tiki-lastchanges.php?days=0" class="btn btn-default" _text="{tr}All{/tr}" _class=$thisclass}
</div>

{if $lastchanges or ($find ne '')}
	{include autocomplete='pagename' file='find.tpl'}
	{if $findwhat != ""}
		{button href="tiki-lastchanges.php" _text="{tr}Search by Date{/tr}"}
	{/if}
{/if}

{if $findwhat!=""}
	{tr}Found{/tr} "<b>{$findwhat|escape}</b>" {tr}in{/tr} {$cant_records|escape} {tr}LastChanges{/tr}
{/if}
<div class="table-responsive">
<table class="table">
	<tr>
		<th>{self_link _sort_arg='sort_mode' _sort_field='lastModif'}{tr}Date{/tr}{/self_link}</th>
		<th>{self_link _sort_arg='sort_mode' _sort_field='object'}{tr}Page{/tr}{/self_link}</th>
		<th>{self_link _sort_arg='sort_mode' _sort_field='action'}{tr}Action{/tr}{/self_link}</th>
		<th>{self_link _sort_arg='sort_mode' _sort_field='user'}{tr}User{/tr}{/self_link}</th>
		{if $prefs.feature_wiki_history_ip ne 'n'}
			<th>{self_link _sort_arg='sort_mode' _sort_field='ip'}{tr}Ip{/tr}{/self_link}</th>
		{/if}
		<th>{self_link _sort_arg='sort_mode' _sort_field='comment'}{tr}Comment{/tr}{/self_link}</th>
		<th>{tr}Action{/tr}</th>
	</tr>

	{section name=changes loop=$lastchanges}
		<tr>
			<td class="date">{$lastchanges[changes].lastModif|tiki_short_datetime}</td>
			<td class="text">
				<a href="{$lastchanges[changes].pageName|sefurl}" class="tablename" title="{$lastchanges[changes].pageName|escape}">
					{$lastchanges[changes].pageName|truncate:$prefs.wiki_list_name_len:"...":true|escape}
				</a>
			</td>
			<td class="text">{tr}{$lastchanges[changes].action|escape}{/tr}</td>
			<td class="username">{$lastchanges[changes].user|userlink}</td>
			{if $prefs.feature_wiki_history_ip ne 'n'}
				<td>{$lastchanges[changes].ip}</td>
			{/if}
			<td class="text">{$lastchanges[changes].comment|escape}</td>
			<td class="action">
				{if $tiki_p_wiki_view_history eq 'y'}
					{if not $lastchanges[changes].current}
						<a class="tips" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}' title=":{tr}History{/tr}">{icon name="history"}</a>{tr}v{/tr}{$lastchanges[changes].version}
	&nbsp;<a class="link" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;preview={$lastchanges[changes].version|escape:"url"}' title="{tr}View{/tr}">v</a>&nbsp;
						{if $tiki_p_rollback eq 'y'}
							<a class="link" href='tiki-rollback.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;version={$lastchanges[changes].version|escape:"url"}' title="{tr}Roll back{/tr}">b</a>&nbsp;
						{/if}
						<a class="link" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;diff={$lastchanges[changes].version|escape:"url"}' title="{tr}Compare{/tr}">c</a>&nbsp;
						<a class="link" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;diff2={$lastchanges[changes].version|escape:"url"}' title="{tr}Diff{/tr}">d</a>&nbsp;
						{if $tiki_p_wiki_view_source eq 'y'}
							<a class="link" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;source={$lastchanges[changes].version|escape:"url"}' title="{tr}Source{/tr}">s</a>
						{/if}
					{else}
						<a class="tips" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}' title=":{tr}History{/tr}">{icon name="history"}</a>
					{/if}
				{/if}
			</td>
		</tr>
	{sectionelse}
		{norecords _colspan=7}
	{/section}
</table>
</div>
{pagination_links cant=$cant_records step=$prefs.maxRecords offset=$offset}{/pagination_links}
