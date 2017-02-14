{* $Id: messu_message_notification.tpl 58620 2016-05-18 13:09:06Z jonnybradley $ *}{$mail_body|truncate:$mail_truncate:"..."}

---
{tr}A new {$prefs.mail_template_custom_text}message was posted to you.{/tr} {tr}Click here to read the full message and / or reply{/tr}:
{$mail_machine}?msgId={$messageid}

{tr}Date:{/tr} {$mail_date|tiki_short_datetime:"":"n"}
