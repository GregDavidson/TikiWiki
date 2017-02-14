{* $Id: forum_outbound.tpl 58620 2016-05-18 13:09:06Z jonnybradley $ *}
{$prefs.mail_template_custom_text}{$title}

{tr}Author:{/tr} {$author|username}

{$data}
{if $reply_link}

--
Reply Link: <{$reply_link}>
{/if}
