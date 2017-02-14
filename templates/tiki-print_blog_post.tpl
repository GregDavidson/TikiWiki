{* $Id: tiki-print_blog_post.tpl 53513 2015-01-16 14:11:32Z luciash $ *}<!DOCTYPE html>
<html id="print" lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}">
	<head>
{include file='header.tpl'}
	</head>
	<body{html_body_attributes}>

		<div id="tiki-clean">
			{include file='blog_wrapper.tpl' blog_post_context='print'}
			<hr>
			<small>
				{tr}Permalink:{/tr} <a class="link" href="{$post_info.postId|sefurl:blogpost}">{$base_url}{$post_info.postId|sefurl:blogpost}</a>
			</small>
		</div>

{include file='footer.tpl'}
	</body>
</html>
