{* $Id: password_reminder.tpl 60261 2016-11-14 13:37:57Z yonixxx $ *}{tr}Hi{/tr} {$mail_user},

{tr}Someone requested a password reset for your {$prefs.mail_template_custom_text}account{/tr} ({$mail_site}).

{tr}Please click on the following link to confirm you wish to reset your password and go to the screen where you must enter a new "permanent" password. Please pick a password only you will know, and don't share it with anyone else.{/tr}
{$mail_machine}/tiki-change_password.php?user={$mail_user|escape:'url'}&actpass={$mail_apass|escape:'url'}

{tr}Important: Username & password are CaSe SenSitiVe{/tr}

{tr}Important: The old password remains active if you don't click the link above.{/tr}

