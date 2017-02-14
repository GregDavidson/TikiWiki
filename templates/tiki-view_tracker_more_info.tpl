{* $Id: tiki-view_tracker_more_info.tpl 48667 2013-11-22 13:55:11Z chibaguy $ *}<!DOCTYPE html>
<html lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}">
	<head>
		{include file='header.tpl'}
	</head>
	<body{html_body_attributes}>
		{if $prefs.feature_bidi eq 'y'}
			<table dir="rtl" ><tr><td>
		{/if}
		<div id="tiki-main" class="alert alert-info">
			<h3>{tr}Details{/tr}</h3>
			<table class="formcolor">
				{if $info.name}
					<tr><td>{tr}Name{/tr}</td><td><b>{$info.name}</b></td></tr>
				{/if}
				{if $info.version}
					<tr><td>{tr}Version{/tr}</td><td><b>{$info.version}</b></td></tr>
				{/if}
				{if $info.longdesc}
					<tr><td colspan="2">{$info.longdesc}</td></tr>
				{/if}
				{if $info.hits}
					<tr><td>{tr}Downloads{/tr}</td><td>{$info.hits}</td></tr>
				{/if}
			</table>
			<div class="panel panel-default">
				<a href="#" onclick="javascript:window.close();" class="link">{tr}close{/tr}</a>
			</div>
		</div>
		{if $prefs.feature_bidi eq 'y'}
			</td></tr></table>
		{/if}
		{include file='footer.tpl'}
	</body>
</html>
