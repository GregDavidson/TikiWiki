{* $Id: tiki-print_article.tpl 55710 2015-06-16 17:01:48Z jonnybradley $ *}<!DOCTYPE html>
<html id="print" lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}">
	<head>
{include file='header.tpl'}
	</head>
	<body{html_body_attributes}>
		<div class="{if $prefs.feature_fixed_width eq 'y'}container{else}container-fluid{/if}">
			<div class="row" id="tiki-clean">
				<div class="col-xs-12">
{include file='article.tpl'}
				</div>
			</div>
		</div>
{include file='footer.tpl'}
	</body>
</html>
