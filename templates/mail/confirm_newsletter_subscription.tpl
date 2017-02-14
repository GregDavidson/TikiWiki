{* $Id: confirm_newsletter_subscription.tpl 58620 2016-05-18 13:09:06Z jonnybradley $ *}{tr}Somebody or you tried to subscribe this email address at our {$prefs.mail_template_custom_text}site:{/tr} {$server_name}
{tr}To the newsletter:{/tr} {$info.name}

{tr}Description:{/tr}
{$info.description}

{tr}In order to confirm your subscription you must access the following URL:{/tr}

{$mail_machine}tiki-newsletters.php?confirm_subscription={$code}
