{* $Id: user_watch_map_changed.tpl 58620 2016-05-18 13:09:06Z jonnybradley $ *}{tr}The {$prefs.mail_template_custom_text}map {$mail_page} was changed by {$mail_user|username} at {$mail_date|tiki_short_datetime:"":"n"}{/tr}

{tr}You can view the updated map following this link:{/tr}
{$mail_machine_raw}/tiki-map.php?mapfile={$mail_page}

{tr}You can edit the map following this link:{/tr}
{$mail_machine}?mapfile={$mail_page}

{tr}If you don't want to receive these notifications follow this link:{/tr}
{$mail_machine_raw}/tiki-user_watches.php?id={$watchId}
