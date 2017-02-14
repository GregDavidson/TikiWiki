{* $Id: tiki-print.tpl 52645 2014-09-26 14:40:18Z jonnybradley $ *}<!DOCTYPE html>
<html lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}"{if !empty($page_id)} id="print_page_{$page_id}"{/if}>
	<head>
{include file='header.tpl'}
	</head>
	<body{html_body_attributes}>

		<div id="tiki-clean">
			<div id="tiki-mid">
{$mid_data}
			</div>
		</div>

{include file='footer.tpl'}
	</body>
</html>
