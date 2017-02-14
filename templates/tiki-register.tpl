{* $Id: tiki-register.tpl 56565 2015-10-25 03:30:35Z lindonb $ *}
{title adminpage='login'}{tr}Registration{/tr}{/title}
{if !empty($showmsg) && $showmsg eq 'y'}
	<div class="simplebox highlight">
		{$msg|nl2br}
	</div>
{/if}
<fieldset>
	{if $user_exists neq true}
		{if !isset($userTrackerHasDescription)}
			<legend>{tr}Register as a new user{/tr}</legend>
		{/if}
	{/if}
	{user_registration}
</fieldset>