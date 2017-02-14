{* $Id: user_validation_waiting_msg.tpl 58620 2016-05-18 13:09:06Z jonnybradley $ *}{if $prefs.login_autogenerate eq 'y'}
	<strong>Your {$prefs.mail_template_custom_text}account ID {$username} has been generated.</strong>
{/if}
{tr}Your account request has been stored and will be activated by the admin as soon as possible.{/tr}
{tr}You'll receive email notification once your account is activated.{/tr}
{tr}Please do not attempt to login until you receive the email notification.{/tr}
