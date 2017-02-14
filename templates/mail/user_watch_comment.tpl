{* $Id: user_watch_comment.tpl 58620 2016-05-18 13:09:06Z jonnybradley $ *}{if $objecttype eq 'wiki'}
{tr}The {$prefs.mail_template_custom_text}Wiki page "{$mail_objectname}" was commented on by{/tr} {if $mail_user}{$mail_user|username}{else}{tr}an anonymous user{/tr}{/if}.
{* Blog comment mail *}
{elseif $objecttype eq 'blog'}
{tr}The {$prefs.mail_template_custom_text}Blog post "{$mail_objectname}" was commented on by{/tr} {if $mail_user}{$mail_user|username}{else}{tr}an anonymous user{/tr}{/if}.
{elseif $objecttype eq 'article'}
{tr}The {$prefs.mail_template_custom_text}article "{$mail_objectname}" was commented on by{/tr} {if $mail_user}{$mail_user|username}{else}{tr}an anonymous user{/tr}{/if}.
{elseif $objecttype eq 'trackeritem'}
{tr}The {$prefs.mail_template_custom_text}tracker item "{$mail_item_title}" of tracker "{$mail_objectname}" was commented on by{/tr} {if $mail_user}{$mail_user|username}{else}{tr}an anonymous user{/tr}{/if}.
{/if}

{tr}You can view the comment by following this link:{/tr}
{if $objecttype eq 'wiki'}
{$mail_machine_raw}/{$mail_objectname|sefurl}#threadId={$comment_id}
{* Blog comment mail *}
{elseif $objecttype eq 'blog'}
{$mail_machine_raw}/{$mail_objectid|sefurl:'blogpost'}#threadId={$comment_id}
{elseif $objecttype eq 'article'}
{$mail_machine_raw}/{$mail_objectid|sefurl:'article'}#threadId={$comment_id}
{elseif $objecttype eq 'trackeritem'}
{$mail_machine_raw}/{$mail_objectid|sefurl:'trackeritem'}#threadId={$comment_id}
{/if}

{tr}Title:{/tr} {$mail_title}
{tr}Comment:{/tr} {$mail_comment}
{tr}Date:{/tr} {$mail_date|tiki_short_datetime:"":"n"}

{if $watchId}
{tr}If you don't want to receive these notifications follow this link:{/tr}
{$mail_machine_raw}/tiki-user_watches.php?id={$watchId}
{/if}

