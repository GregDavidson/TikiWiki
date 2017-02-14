{* $Id: tiki-list_invite.tpl 56013 2015-08-07 14:09:32Z markitosaad $ *}
{title}{tr}Invitations list{/tr}{/title}

<div class="t_avbar">
	{button href="tiki-invite.php" class="btn btn-default" _text="{tr}Invite{/tr}"}
	{if $tiki_p_admin eq 'y'}{button href="tiki-adminusers.php" _text="{tr}Admin users{/tr}"}{/if}
</div>

<div class="clearfix">
	<form class="text-center" action="tiki-list_invite.php" method="post">
		{if $tiki_p_admin eq 'y'}
			<label>
				{tr}Inviter:{/tr}
				<input type="text" name="inviter" value="{$inviter|escape}">
			</label>
		{/if}
		<label>
			{tr}Only successful invitations:{/tr}
			<input type="checkbox" name="only_success"{if $only_success eq 'y'} checked="checked"{/if}>
		</label>
		<label>
			{tr}Only pending invitations:{/tr}
			<input type="checkbox" name="only_pending"{if $only_pending eq 'y'} checked="checked"{/if}>
		</label>
		<br>
		<input type="submit" class="btn btn-default btn-sm" name="filter" value="{tr}Filter{/tr}">
	</form>
</div>

{tr}Number of invitations:{/tr} {$cant}
{if $cant > 0}
	<div class="table-responsive">
		<table class="table">
			<tr>
				{if $tiki_p_admin eq 'y'}
					<th>{self_link _sort_arg='sort_mode' _sort_field='inviter'}{tr}Inviter{/tr}{/self_link}</th>
				{/if}
				<th>{self_link _sort_arg='sort_mode' _sort_field='ts'}{tr}Date{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode' _sort_field='email'}{tr}Email{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode' _sort_field='status'}{tr}Status{/tr}{/self_link}</th>
			</tr>

			{foreach item=invited from=$inviteds}
				<tr>
					{if $tiki_p_admin eq 'y'}
						<td class="text">{$invited.inviter|userlink}</td>
					{/if}
					<td class="date">{$invited.ts|tiki_short_date}</td>
					<td class="email">{$invited.email|escape}</td>
					<td class="text">{$invited.used|escape}</td>
				</tr>
			{/foreach}
		</table>
	</div>
{/if}

{pagination_links cant=$cant step=$max offset=$offset}{/pagination_links}
