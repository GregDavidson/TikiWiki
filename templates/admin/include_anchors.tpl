{*$Id: include_anchors.tpl 60181 2016-11-07 11:00:44Z jonnybradley $*}
{foreach from=$admin_icons key=page item=info}
	{if ! $info.disabled}
		<li><a href="tiki-admin.php?page={$page}" alt="{$info.title} {$info.description}" class="tips bottom slow icon text-muted" title="{$info.title}|{$info.description}">
			{icon name="admin_$page"}
		</a></li>
	{/if}
{/foreach}