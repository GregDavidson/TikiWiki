{* $Id: share.tpl 58620 2016-05-18 13:09:06Z jonnybradley $ *}{if !empty($comment)}
{$prefs.mail_template_custom_text}{$comment}
{else}
{tr}Look at this {$prefs.mail_template_custom_text}link:{/tr}
{/if}
{$url_for_friend|replace:' ':'+'}
-
{$name|username}
{$email}
