{*Smarty template*}
{* start ************ Task View ***************}

<h2>{$info.title|escape}</h2>&nbsp;

{if ($info.user eq $user) or ($info.creator eq $user) or ($admin_mode)}
	<a href="tiki-user_tasks.php?taskId={$taskId}&amp;tiki_view_mode=edit" class="tablink tips" title=":{tr}Edit{/tr}">
		{icon name='edit'}
	</a>&nbsp;
	<a href="tiki-user_tasks.php?taskId={$taskId}&amp;save=on&amp;task_accept=on" class="tablink tips"  title=":{tr}Accept{/tr}">
		{icon name='ok'}
	</a>&nbsp;
	<a href="tiki-user_tasks.php?taskId={$taskId}&amp;save=on&amp;task_not_accept=on" class="tablink tips" title=":{tr}Reject{/tr}">
		{icon name='remove'}
	</a>&nbsp;
	<a href="tiki-user_tasks.php?taskId={$taskId}&amp;save=on&amp;move_into_trash=on" class="tablink tips" title=":{tr}Move to trash{/tr}">
		{icon name='trash'}
	</a>&nbsp;
{/if}
<br><br>

<div class="tabcontent" style="width:99%">
	{if ($tiki_view_mode eq 'preview')}
		<div align="center" class="attention" style="font-weight:bold">
			{tr}Note: Remember that this is only a preview, and has not yet been saved!{/tr}
		</div>
	{/if}
	{if ($tiki_view_mode eq 'view')}
		<div style="text-align:right;">
		{if ($info.task_version > 0)}
			<a class="link tips" title=":{tr}Previous version{/tr}" href="tiki-user_tasks.php?taskId={$taskId}&amp;tiki_view_mode=view&amp;show_history={$info.task_version-1}">{icon name='previous'}</a>
		{/if}
			{tr}version:{/tr} <b>{$info.task_version+1}</b>
			{if $info.task_version < $info.last_version}
				<a class="link tips" title=":{tr}Next version{/tr}" href="tiki-user_tasks.php?taskId={$taskId}&amp;tiki_view_mode=view&amp;show_history={$info.task_version+1}">
					{icon name='next'}
				</a>
		{/if}
		</div>
	{/if}
	<table style="border:0;width:100%;">
		<tr>
			<td class="prio{$info.priority}" style="border:0;">
				<table class="prio{$info.priority}" style="border:0;">
					<tr>
						<td style="font-weight:bold;">
							{tr}Start:{/tr}
						</td>
						<td>
							{$info.start|tiki_short_date}&nbsp;--&nbsp;{$info.start|tiki_short_time}
						</td>
					</tr>
					<tr>
						<td style="font-weight:bold;">
							{tr}End:{/tr}
						</td>
						<td>
							<b>{$info.end|tiki_short_date}&nbsp;--&nbsp;{$info.end|tiki_short_time}</b>
						</td>
					</tr>
					<tr>
						<td style="font-weight:bold;">
							{tr}Status:{/tr}
						</td>
						<td >
							{if $info.status eq ''}
								{tr}Waiting / Not Started{/tr}
							{/if}
							{if $info.status eq 'o'}
								{tr}Open / In Process{/tr}
							{/if}
							{if $info.status eq 'c'}
								{tr}completed (100%){/tr}
							{/if}
							&nbsp;&nbsp;
							<b>{$info.completed|tiki_short_date}&nbsp;--&nbsp;{$info.completed|tiki_short_time}</b>
						</td>
					</tr>
					<tr>
						<td style="font-weight:bold;">
							{tr}Priority:{/tr}
						</td>
						<td >
							{$info.priority}
						</td>
					</tr>
					<tr>
						<td style="font-weight:bold;">
							{tr}Percentage completed:{/tr}
						</td>
						<td >
							{$info.percentage}%
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<table style="width:100%;border:0">
		<tr>
			<td>
				<div class="articlebody">
					{$info.parsed}
				</div>
			</td>
		</tr>
	</table>

	{tr}Created by:{/tr} {$info.creator|userlink} {tr}for:{/tr} {$info.user|userlink}.
	{if ($info.task_version > 0 ) and ($info.creator ne $info.user)}
		{tr}Last modified by:{/tr} {$info.lasteditor|escape|userlink} on {$info.changes|tiki_short_date}&nbsp;--&nbsp;{$info.changes|tiki_short_time}
	{/if}
	<br>
	{if $info.public_for_group ne ''}
		{tr}Public for group:{/tr}{$info.public_for_group|escape}<br>
	{/if}
	{if $info.creator ne $info.user}
		{tr}Accepted by User:{/tr}
			{if $info.accepted_user eq 'y'}
				{tr}Yes{/tr}
			{else}
				{if $info.accepted_user eq 'n'}
					{tr}No / Rejected{/tr}
				{else}
					{tr}Waiting{/tr}
				{/if}
			{/if}<br>
		{tr}Accepted by Creator:{/tr}
		{if $info.accepted_creator eq 'y'}
			{tr}Yes{/tr}
		{else}
			{if $info.accepted_creator eq 'n'}
				{tr}No / Rejected{/tr}
			{else}
				{tr}Waiting{/tr}
			{/if}
		{/if}<br>
	{/if}
</div>
<br>
<br>
{* end ************ Task list ***************}
