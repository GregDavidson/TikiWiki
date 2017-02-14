{* $Id: user_watch_category_created.tpl 58620 2016-05-18 13:09:06Z jonnybradley $ *}{tr}A new {$prefs.mail_template_custom_text}category was created in:{/tr} {$parentName}

{tr}Created by:{/tr} {$author|username}
{tr}Date:{/tr} {$mail_date|tiki_short_datetime:"":"n"}
{tr}Name:{/tr} {$categoryName}
{tr}Path:{/tr} {$categoryPath}
{tr}Description:{/tr} {$description}

{$mail_machine}/{$categoryId|sefurl:category}