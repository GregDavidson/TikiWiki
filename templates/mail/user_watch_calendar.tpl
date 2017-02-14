{* $Id: user_watch_calendar.tpl 58620 2016-05-18 13:09:06Z jonnybradley $ *}{$prefs.mail_template_custom_text}{$mail_data.name}
---

{$mail_data.description}

{tr}From{/tr}:	{$mail_data.start|tiki_long_datetime}
{tr}to{/tr}:		{$mail_data.end|tiki_long_datetime}

{tr}View item calendar at:{/tr}
{$mail_machine}/tiki-calendar_edit_item.php?viewcalitemId={$mail_calitemId}&calIds[]={$mail_data.calendarId}
