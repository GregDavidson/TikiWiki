{* $Id: user_watch_image_gallery_upload.tpl 58620 2016-05-18 13:09:06Z jonnybradley $ *}
{tr}A new file was posted to {$prefs.mail_template_custom_text}image gallery:{/tr} {$galleryName}

{tr}Posted by:{/tr} {$author|username}
{tr}Date:{/tr} {$mail_date|tiki_short_datetime:"":"n"}
{tr}Name:{/tr} {$fname}
{tr}File Name:{/tr} {$filename}
{tr}File Description:{/tr} {$description}

You can see the new image at:
{$mail_machine_raw}/tiki-browse_image.php?galleryId={$galleryId}&imageId={$imageId}
