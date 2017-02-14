{* $Id: newsletter_welcome.tpl 58620 2016-05-18 13:09:06Z jonnybradley $ *}{tr}Welcome to our newsletter!{/tr}
{tr}This email address has been added to the list of subscribers of: {/tr}
{$prefs.mail_template_custom_text}{tr}Newsletter:{/tr} {$info.name}

{tr}Description:{/tr}
{$info.description}

{tr}You can always cancel your subscription using:{/tr}

{$url_subscribe}?unsubscribe={$code}