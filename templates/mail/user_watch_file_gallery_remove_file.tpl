{* $Id: user_watch_file_gallery_remove_file.tpl 58620 2016-05-18 13:09:06Z jonnybradley $ *}{tr}A file was removed from the {$prefs.mail_template_custom_text}file gallery:{/tr} {$galleryName}

{tr}Removed by:{/tr} {$author|username}
{tr}Name:{/tr} {$fname}
{tr}File Name:{/tr} {$filename}
{tr}Date:{/tr} {$mail_date|tiki_short_datetime:"":"n"}

You can view the updated file gallery at:
{$mail_machine}/{$galleryId|sefurl:'file gallery'}

