{* $Id: tiki-sqllog.tpl 56013 2015-08-07 14:09:32Z markitosaad $ *}
{title admpage="general"}{tr}Log SQL{/tr}{/title}

{if $prefs.log_sql ne 'y'}
	{remarksbox type="warning" title="{tr}Notice{/tr}"}{tr}This feature is disabled{/tr}<br>{tr}You will not see the latest queries.{/tr}{/remarksbox}
{/if}

<div class="t_navbar">
	{button href="?clean=y" class="btn btn-default" _text="{tr}Clean{/tr}"}
</div>

{include file='find.tpl' find_show_num_rows='y'}

<div class="table-responsive">
<table class="table">
	<tr>
		<th>{self_link _sort_arg='sort_mode' _sort_field='created'}{tr}Created{/tr}{/self_link}</th>
		<th>{self_link _sort_arg='sort_mode' _sort_field='sql1'}{tr}Query{/tr}{/self_link}</th>
		<th>{self_link _sort_arg='sort_mode' _sort_field='params'}{tr}Params{/tr}{/self_link}</th>
		<th>{self_link _sort_arg='sort_mode' _sort_field='tracer'}{tr}From{/tr}{/self_link}</th>
		<th>{self_link _sort_arg='sort_mode' _sort_field='timer'}{tr}Time{/tr}{/self_link}</th>
	<tr>
	{foreach from=$logs item=log}
		<tr>
			<td class="text">{$log.created|escape}</td>
			<td class="text">{$log.sql1|escape}</td>
			<td class="text">{$log.params|escape}</td>
			<td class="text">{$log.tracer|escape}</td>
			<td class="date">{$log.timer|escape}</td>
		</tr>
	{/foreach}
</table>
</div>

{pagination_links cant=$cant step=$numrows offset=$offset}{/pagination_links}
