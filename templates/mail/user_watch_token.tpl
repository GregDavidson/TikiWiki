{* $Id: user_watch_token.tpl 58620 2016-05-18 13:09:06Z jonnybradley $ *}<a href="mailto:{$email_token}">{$email_token}</a> {tr}has consulted your{/tr} {$prefs.mail_template_custom_text}

{if $filegallery eq 'y'}
	{tr}file{/tr} : {$filename}
	<br>
	<br>
	<a href="{$prefix_url}/tiki-list_file_gallery.php?galleryId={$filegalleryId}">&raquo; {tr}Go to the File Gallery{/tr}</a><br>
	<a href="{$prefix_url}/tiki-download_file.php?fileId={$fileId}">&raquo; {tr}Download the file:{/tr} {$filename}</a><br>
{else}
	{tr}page{/tr} <a href="{$page_token}">{$page_token}</a>
{/if}
