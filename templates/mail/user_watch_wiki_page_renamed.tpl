{* $Id: user_watch_wiki_page_renamed.tpl 58620 2016-05-18 13:09:06Z jonnybradley $ *}{$prefs.mail_template_custom_text}{tr}Wiki page renamed{/tr} {tr}by{/tr} {$mail_user|username}.

{tr}Date:{/tr} {$mail_date|tiki_short_datetime:"":"n"}

{tr}Old name:{/tr} {$mail_oldname}
{tr}New name:{/tr} {$mail_newname}

{tr}If you don't want to receive these notifications follow this link:{/tr}
{$mail_machine_raw}/tiki-user_watches.php?id={$watchId}
