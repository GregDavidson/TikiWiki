{* $Id: tiki-accounting_account_view.tpl 54656 2015-03-22 17:54:02Z lindonb $ *}
{title help="accounting"}
	{$book.bookName}:
	{tr}View account{/tr} {$account.accountId} {$account.accountName}
{/title}
{if !empty($errors)}
	<div class="alert alert-warning">
		{icon name='error' alt="{tr}Error{/tr}" style="vertical-align:middle" align="left"}
		{foreach from=$errors item=m name=errors}
			{$m}
			{if !$smarty.foreach.errors.last}<br>{/if}
		{/foreach}
	</div>
{/if}
<div id="account_view">
	<div>
		<div class="aclabel">{tr}Account number{/tr}</div>
		{$account.accountId}
	</div>
	<div>
		<div class="aclabel">{tr}Account name{/tr}</div>
		{$account.accountName}
	</div>
	<div>
		<div class="aclabel">{tr}Notes{/tr}</div>
		{$account.accountNotes}
	</div>
	<div>
		<div class="aclabel">{tr}Budget{/tr}</div>
		{if $book.bookCurrencyPos==-1}{$book.bookCurrency} {/if}{$account.accountBudget|number_format:$book.bookDecimals:$book.bookDecPoint:$book.bookThousand}{if $book.bookCurrencyPos==1} {$book.bookCurrency}{/if}
	</div>
	<div>
	<div class="aclabel">{tr}Locked{/tr}</div>
		{if $account.accountLocked==1}{tr}Yes{/tr}{else}{tr}No{/tr}{/if}
	</div>
	{button href="tiki-accounting.php?bookId=$bookId" _text="Back to book page"}
	{if $tiki_p_acct_manage_accounts=='y'}
		{button href="tiki-accounting_account.php?action=edit&bookId=$bookId&accountId={$account.accountId}" _text="{tr}Edit this account{/tr}"}
		{if $account.changeable==1}{button href="tiki-accounting_account.php?action=delete&bookId=$bookId&accountId={$account.accountId}" _text="{tr}Delete this account{/tr}"}{/if}
	{/if}
</div>
{if isset($journal)}
	<div id="account_journal">
		{include file='tiki-accounting_journal.tpl'}
	</div>
{/if}

