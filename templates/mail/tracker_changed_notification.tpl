{* $Id: tracker_changed_notification.tpl 58620 2016-05-18 13:09:06Z jonnybradley $ *}{if $mail_action eq 'deleted'}{tr}ItemID {$mail_itemId} {$mail_item_desc} was deleted in the {$prefs.mail_template_custom_text}tracker {tr}{$mail_trackerName}{/tr}{/tr}
{elseif $mail_action eq 'status'}{tr}New status for ItemID {$mail_itemId} {$mail_item_desc} for the {$prefs.mail_template_custom_text}tracker {tr}{$mail_trackerName}{/tr}:{/tr} {if $status eq 'o'}{tr}open{/tr}{elseif $status eq 'p'}{tr}pending{/tr}{elseif $status eq 'c'}{tr}closed{/tr}{/if}
{else}{$mail_action}

{tr}View the {$prefs.mail_template_custom_text}tracker item at:{/tr}
	{$mail_machine_raw}/{$mail_itemId|sefurl:'trackeritem'}
{/if}

{tr}Author:{/tr} {$mail_user|username}
{tr}Date:{/tr} {$mail_date|tiki_short_datetime:"":"n"}

{$mail_data|replace:'-[':''|replace:']-':''}{* TODO: translate these -[...]- marked strings in $mail_data by watcher language *}
{* {$mail_data|replace:"\n\n":"\n"|replace:":\n":": "} to reduce the number of line *}

{if isset($mail_attId)}
	{tr}Download the file at:{/tr} {$mail_machine_raw}/tiki-download_item_attachment.php?attId={$mail_attId}
{/if}
