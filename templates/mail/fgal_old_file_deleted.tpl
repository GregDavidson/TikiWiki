{* $Id: fgal_old_file_deleted.tpl 58620 2016-05-18 13:09:06Z jonnybradley $ *}{tr}Remove {$prefs.mail_template_custom_text}file{/tr}
---
{tr}Gallery:{/tr} {$galInfo.name}
{tr}GalleryId:{/tr} {$galInfo.galleryId}

{tr}Identifier:{/tr} {$fileInfo.fileId}
{tr}Filename:{/tr} {$fileInfo.filename}
{tr}Name:{/tr} {$fileInfo.name}
{tr}Description:{/tr} {$fileInfo.description}
{tr}Creator:{/tr} {$fileInfo.user|username}
{tr}Author:{/tr} {$fileInfo.author|username}
{tr}Created:{/tr} {$fileInfo.created|tiki_long_datetime}
{tr}Last editor:{/tr} {$fileInfo.lastModifUser|username}
{tr}Last modified:{/tr} {$fileInfo.lastModif|tiki_long_datetime}
{tr}Last download:{/tr} {$fileInfo.lastDownload|tiki_long_datetime}
