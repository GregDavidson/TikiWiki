{* $Id: tiki-directory_admin.tpl 57907 2016-03-16 13:23:47Z patrick-proulx $ *}

{title help="Directory" admpage="directory"}{tr}Admin Directory{/tr}{/title}

{include file='tiki-directory_admin_bar.tpl'} <br>
<h2>{tr}Statistics{/tr}</h2>
{tr}There are{/tr} {$stats.invalid} {tr}invalid sites{/tr}<br>
{tr}There are{/tr} {$stats.valid} {tr}valid sites{/tr}<br>
{tr}There are{/tr} {$stats.categs} <a class="link" href="tiki-directory_admin_categories.php">{tr}Directory Categories{/tr}</a><br>
{tr}Users have visited{/tr} {$stats.visits} {tr}sites from the directory{/tr}<br>
{tr}Users have searched{/tr} {$stats.searches} {tr}times from the directory{/tr}<br>
