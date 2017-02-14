{* $Id: header.tpl 60342 2016-11-21 12:53:10Z yonixxx $ *}
{if $base_uri and ($dir_level gt 0 or $prefs.feature_html_head_base_tag eq 'y')}
	<base href="{$base_uri|escape}">
{/if}

<!--Latest IE Compatibility-->
<meta http-equiv="X-UA-Compatible" content="IE=Edge">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="generator" content="Tiki Wiki CMS Groupware - https://tiki.org">

{* --- SocialNetwork:Domain ---*}
<meta content="{$base_url_canonical}" property="article:publisher">
<meta content="{$base_url_canonical}" name="twitter:domain"> {* may be obsolete when using twitter:card *}


{* --- Canonical URL --- *}
{include file="canonical.tpl"}


{if !empty($forum_info.name) & $prefs.metatag_threadtitle eq 'y'}
	<meta name="keywords" content="{tr}Forum{/tr} {$forum_info.name|escape} {$thread_info.title|escape} {if $prefs.feature_freetags eq 'y'}{foreach from=$freetags.data item=taginfo}{$taginfo.tag|escape} {/foreach}{/if}">
{elseif isset($galleryId) && $galleryId neq '' && $prefs.metatag_imagetitle neq 'n'}
	<meta name="keywords" content="{tr}Images Galleries{/tr} {$title|escape} {if $prefs.feature_freetags eq 'y'}{foreach from=$freetags.data item=taginfo}{$taginfo.tag|escape} {/foreach}{/if}">
{elseif $prefs.metatag_keywords neq '' or !empty($metatag_local_keywords)}
	<meta name="keywords" content="{$prefs.metatag_keywords|escape} {if $prefs.feature_freetags eq 'y'}{foreach from=$freetags.data item="taginfo"}{$taginfo.tag|escape} {/foreach}{/if} {$metatag_local_keywords|escape}">
{/if}
{if $prefs.metatag_author neq ''}
	<meta name="author" content="{$prefs.metatag_author|escape}">
{/if}

{* --- Blog description --- *}
{if isset($section) and $section eq "blogs"}
	{if not empty($post_info.parsed_excerpt)}
		{$metatag_description = $post_info.parsed_excerpt|strip_tags:false|truncate:200|escape}
	{elseif not empty($post_info.parsed_data|strip_tags)}
		{$metatag_description = $post_info.parsed_data|strip_tags:false|truncate:200|escape}
	{else}
		{$metatag_description = $post_info.title|cat:' - '|cat:$blog_data.title|escape}
	{/if}
	{* --- Article description --- *}
{elseif isset($section) and $section eq "cms"}
	{if not empty($heading)}
		{$metatag_description = $parsed_heading|strip_tags:false|truncate:200|escape}
	{elseif not empty ($body)}
		{$metatag_description = $parsed_body|strip_tags:false|truncate:200|escape}
	{/if}
{elseif $prefs.metatag_pagedesc eq 'y' and not empty($metatag_description)}
	{$metatag_description = $metatag_description|escape}
{elseif not empty($prefs.metatag_description)}
	{$metatag_description = $prefs.metatag_description|escape}
{/if}

{if not empty($metatag_description) and not empty($metatag_description|trim)}
	<meta name="description" content="{$metatag_description}" property="og:description">
	<meta name="twitter:description" content="{$metatag_description}">
{else}
	<meta name="description" content="{$prefs.browsertitle|tr_if|escape} {$prefs.site_nav_seper}{if isset($title)} {$title}{/if}" property="og:description">
	<meta name="twitter:description" content="{$prefs.browsertitle|tr_if|escape} {$prefs.site_nav_seper}{if isset($title)} {$title}{/if}">
{/if}

{if $prefs.metatag_geoposition neq ''}
	<meta name="geo.position" content="{$prefs.metatag_geoposition|escape}">
{/if}
{if $prefs.metatag_georegion neq ''}
	<meta name="geo.region" content="{$prefs.metatag_georegion|escape}">
{/if}
{if $prefs.metatag_geoplacename neq ''}
	<meta name="geo.placename" content="{$prefs.metatag_geoplacename|escape}">
{/if}
{if (isset($prefs.metatag_robots) and $prefs.metatag_robots neq '') and (!isset($metatag_robots) or $metatag_robots eq '')}
	<meta name="robots" content="{$prefs.metatag_robots|escape}">
{/if}
{if (!isset($prefs.metatag_robots) or $prefs.metatag_robots eq '') and (isset($metatag_robots) and $metatag_robots neq '')}
	<meta name="robots" content="{$metatag_robots|escape}">
{/if}
{if (isset($prefs.metatag_robots) and $prefs.metatag_robots neq '') and (isset($metatag_robots) and $metatag_robots neq '')}
	<meta name="robots" content="{$prefs.metatag_robots|escape}, {$metatag_robots|escape}">
{/if}
{if $prefs.metatag_revisitafter neq ''}
	<meta name="revisit-after" content="{$prefs.metatag_revisitafter|escape}">
{/if}

{* --- SocialNetwork:site_name --- *}
<meta content="{if not empty($prefs.socialnetworks_facebook_site_name)}{$prefs.socialnetworks_facebook_site_name}{else}{$prefs.browsertitle|tr_if|escape}{/if}" property="og:site_name">
<meta content="{if not empty($prefs.socialnetworks_twitter_site)}{$prefs.socialnetworks_twitter_site}{else}{$prefs.browsertitle|tr_if|escape}{/if}" name="twitter:site">

{* --- SocialNetwork: fb:app_id ---*}
{if not empty($prefs.socialnetworks_facebook_application_id)}<meta content="{$prefs.socialnetworks_facebook_application_id}" property="fb:app_id">{/if}

{* --- tiki block --- *}
<title>{strip}
{if !empty($sswindowtitle)}
	{if $sswindowtitle eq 'none'}
		&nbsp;
	{else}
		{$sswindowtitle|escape}
	{/if}
{else}
	{if $prefs.site_title_location eq 'before'}{$prefs.browsertitle|tr_if|escape} {$prefs.site_nav_seper} {/if}
	{capture assign="page_description_title"}
		{if ($prefs.feature_breadcrumbs eq 'y' or $prefs.site_title_breadcrumb eq "desc") && isset($trail)}
			{breadcrumbs type=$prefs.site_title_breadcrumb loc="head" crumbs=$trail}
		{/if}
	{/capture}
	{if isset($structure) and $structure eq 'y'} {* get the alias name if item is a wiki page and it is in a structure *}
		{section loop=$structure_path name=ix}
		{assign var="aliasname" value={$structure_path[ix].page_alias}}
		{/section}
	{/if}
	{if !empty($page_description_title)}
		{$page_description_title}
	{else}
		{if !empty($tracker_item_main_value)}
			{$tracker_item_main_value|truncate:255|escape}
		{elseif !empty($title) and !is_array($title)}
			{$title|escape}
		{elseif !empty($aliasname)}
			{$aliasname|escape}
		{elseif !empty($page)}
			{$page|escape}
		{elseif !empty($arttitle)}
			{$arttitle|escape}
		{elseif !empty($thread_info.title)}
			{$thread_info.title|escape}
		{elseif !empty($forum_info.name)}
			{$forum_info.name|escape}
		{elseif !empty($categ_info.name)}
			{$categ_info.name|escape}
		{elseif !empty($userinfo.login)}
			{$userinfo.login|username}
		{elseif !empty($tracker_info.name)}
			{$tracker_info.name|escape}
		{elseif !empty($headtitle)}
			{$headtitle|stringfix:"&nbsp;"|escape}{* use $headtitle last if feature specific title not found *}
		{elseif !empty($description)}
			{$description|escape}{* use description if nothing else is found but this is likely to contain tiki markup *}
			{* add $description|escape if you want to put the description + update breadcrumb_build replace return $crumbs->title; with return empty($crumbs->description)? $crumbs->title: $crumbs->description; *}
		{/if}
	{/if}
	{if $prefs.site_title_location eq 'after'} {$prefs.site_nav_seper} {$prefs.browsertitle|tr_if|escape}{/if}
{/if}
{/strip}</title>

{* --- SocialNetwork:title --- *}
{* Facebook *}
<meta content="{strip}
		{if !empty($sswindowtitle)}
			{if $sswindowtitle eq 'none'}
				&nbsp;
			{else}
				{$sswindowtitle|escape}
			{/if}
		{else}
			{if $prefs.site_title_location eq 'before'}{$prefs.browsertitle|tr_if|escape} {$prefs.site_nav_seper} {/if}
			{capture assign="page_description_title"}
				{if ($prefs.feature_breadcrumbs eq 'y' or $prefs.site_title_breadcrumb eq "desc") && isset($trail)}
					{breadcrumbs type=$prefs.site_title_breadcrumb loc="head" crumbs=$trail}
				{/if}
			{/capture}
			{if isset($structure) and $structure eq 'y'} {* get the alias name if item is a wiki page and it is in a structure *}
				{section loop=$structure_path name=ix}
					{assign var="aliasname" value={$structure_path[ix].page_alias}}
				{/section}
			{/if}
			{if !empty($page_description_title)}
				{$page_description_title}
			{else}
				{if !empty($tracker_item_main_value)}
					{$tracker_item_main_value|truncate:255|escape}
				{elseif !empty($title) and !is_array($title)}
					{$title|escape}
				{elseif !empty($aliasname)}
					{$aliasname|escape}
				{elseif !empty($page)}
					{$page|escape}
				{elseif !empty($description)}{$description|escape}
					{* add $description|escape if you want to put the description + update breadcrumb_build replace return $crumbs->title; with return empty($crumbs->description)? $crumbs->title: $crumbs->description; *}
				{elseif !empty($arttitle)}
					{$arttitle|escape}
				{elseif !empty($thread_info.title)}
					{$thread_info.title|escape}
				{elseif !empty($forum_info.name)}
					{$forum_info.name|escape}
				{elseif !empty($categ_info.name)}
					{$categ_info.name|escape}
				{elseif !empty($userinfo.login)}
					{$userinfo.login|username}
				{elseif !empty($tracker_info.name)}
					{$tracker_info.name|escape}
				{elseif !empty($headtitle)}
					{$headtitle|stringfix:"&nbsp;"|escape}{* use $headtitle last if feature specific title not found *}
				{/if}
			{/if}
			{if $prefs.site_title_location eq 'after'} {$prefs.site_nav_seper} {$prefs.browsertitle|tr_if|escape}{/if}
		{/if}
	{/strip}
" property="og:title">

{* Twitter *}
<meta content="{strip}
		{if !empty($sswindowtitle)}
			{if $sswindowtitle eq 'none'}
				&nbsp;
			{else}
				{$sswindowtitle|escape}
			{/if}
		{else}
			{if $prefs.site_title_location eq 'before'}{$prefs.browsertitle|tr_if|escape} {$prefs.site_nav_seper} {/if}
			{capture assign="page_description_title"}
				{if ($prefs.feature_breadcrumbs eq 'y' or $prefs.site_title_breadcrumb eq "desc") && isset($trail)}
					{breadcrumbs type=$prefs.site_title_breadcrumb loc="head" crumbs=$trail}
				{/if}
			{/capture}
			{if isset($structure) and $structure eq 'y'} {* get the alias name if item is a wiki page and it is in a structure *}
				{section loop=$structure_path name=ix}
					{assign var="aliasname" value={$structure_path[ix].page_alias}}
				{/section}
			{/if}
			{if !empty($page_description_title)}
				{$page_description_title}
			{else}
				{if !empty($tracker_item_main_value)}
					{$tracker_item_main_value|truncate:255|escape}
				{elseif !empty($title) and !is_array($title)}
					{$title|escape}
				{elseif !empty($aliasname)}
					{$aliasname|escape}
				{elseif !empty($page)}
					{$page|escape}
				{elseif !empty($description)}{$description|escape}
					{* add $description|escape if you want to put the description + update breadcrumb_build replace return $crumbs->title; with return empty($crumbs->description)? $crumbs->title: $crumbs->description; *}
				{elseif !empty($arttitle)}
					{$arttitle|escape}
				{elseif !empty($thread_info.title)}
					{$thread_info.title|escape}
				{elseif !empty($forum_info.name)}
					{$forum_info.name|escape}
				{elseif !empty($categ_info.name)}
					{$categ_info.name|escape}
				{elseif !empty($userinfo.login)}
					{$userinfo.login|username}
				{elseif !empty($tracker_info.name)}
					{$tracker_info.name|escape}
				{elseif !empty($headtitle)}
					{$headtitle|stringfix:"&nbsp;"|escape}{* use $headtitle last if feature specific title not found *}
				{/if}
			{/if}
			{if $prefs.site_title_location eq 'after'} {$prefs.site_nav_seper} {$prefs.browsertitle|tr_if|escape}{/if}
		{/if}
	{/strip}
" name="twitter:title">

{* --- SocialNetwork:type --- *}

{if $prefs.feature_canonical_url eq 'y' and isset($mid)}
	{if $mid eq 'tiki-view_blog.tpl'}
		<meta content="blog" property="og:type">
	{elseif $mid eq 'tiki-view_blog_post.tpl'}
		<meta content="blog" property="og:type">
	{elseif $mid eq 'tiki-read_article.tpl'}
		<meta content="article" property="og:type">
	{else}
		<meta content="website" property="og:type">
	{/if}
{/if}

{* To be added someday when using cart feature: product, product.group, product.item *}
{* May be usefull too : profile *}

<meta name="twitter:card" content="summary">

{* --- SocialNetwork:image --- *}

{if $prefs.feature_canonical_url eq 'y' and isset($mid)}
	{if $mid eq 'tiki-view_blog.tpl'}
	{elseif $mid eq 'tiki-view_blog_post.tpl'}
{* --- Article --- *}
	{elseif $mid eq 'tiki-read_article.tpl'}
		<meta content="{$base_url_canonical}{if $hasImage eq 'y'}article_image.php?image_type=article&amp;id={$articleId}{else}article_image.php?image_type=topic&amp;id={$topicId}{/if}" property="og:image">
		<meta content="{$base_url_canonical}{if $hasImage eq 'y'}article_image.php?image_type=article&amp;id={$articleId}{else}article_image.php?image_type=topic&amp;id={$topicId}{/if}" name="twitter:image">
	{else}
		<meta content="{$prefs.socialnetworks_facebook_site_image}" property="og:image">
		<meta content="{$prefs.socialnetworks_twitter_site_image}" name="twitter:image">
	{/if}
{/if}

{if $favicon_touch == true}<link rel="apple-touch-icon" sizes="180x180" href="img/favicons/apple-touch-icon.png">{/if}
{if $favicon_32 == true}<link rel="icon" type="image/png" href="img/favicons/favicon-32x32.png" sizes="32x32">{/if}
{if $favicon_16 == true}<link rel="icon" type="image/png" href="img/favicons/favicon-16x16.png" sizes="16x16">{/if}
{if $favicon_json == true}<link rel="manifest" href="img/favicons/manifest.json">{/if}
{if $favicon_pinned == true}<link rel="mask-icon" href="img/favicons/safari-pinned-tab.svg" color="#5bbad5">{/if}
{if $favicon_xml == true}<meta name="msapplication-config" content="img/favicons/browserconfig.xml">{/if}

{* --- universaleditbutton.org --- *}
{if (isset($editable) and $editable) and ($tiki_p_edit eq 'y' or $page|lower eq 'sandbox' or $tiki_p_admin_wiki eq 'y')}
	<link rel="alternate" type="application/x-wiki" title="{tr}Edit this page!{/tr}" href="tiki-editpage.php?page={$page|escape:url}">
{/if}

{* --- Firefox RSS icons --- *}
{if $prefs.feature_wiki eq 'y' and $prefs.feed_wiki eq 'y' and $tiki_p_view eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.feed_wiki_title|escape|default:"{tr}RSS Wiki{/tr}"}' href="tiki-wiki_rss.php?ver={$prefs.feed_default_version|escape:'url'}">
{/if}
{if $prefs.feature_blogs eq 'y' and $prefs.feed_blogs eq 'y' and $tiki_p_read_blog eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.feed_blogs_title|escape|default:"{tr}RSS Blogs{/tr}"}' href="tiki-blogs_rss.php?ver={$prefs.feed_default_version|escape:'url'}">
{/if}
{if $prefs.feature_articles eq 'y' and $prefs.feed_articles eq 'y' and $tiki_p_read_article eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.feed_articles_title|escape|default:"{tr}RSS Articles{/tr}"}' href="tiki-articles_rss.php?ver={$prefs.feed_default_version|escape:'url'}">
{/if}
{if $prefs.feature_galleries eq 'y' and $prefs.feed_image_galleries eq 'y' and $tiki_p_view_image_gallery eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.feed_image_galleries_title|escape|default:"{tr}RSS Image Galleries{/tr}"}' href="tiki-image_galleries_rss.php?ver={$prefs.feed_default_version}">
{/if}
{if $prefs.feature_file_galleries eq 'y' and $prefs.feed_file_galleries eq 'y' and $tiki_p_view_file_gallery eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.feed_file_galleries_title|escape|default:"{tr}RSS File Galleries{/tr}"}' href="tiki-file_galleries_rss.php?ver={$prefs.feed_default_version|escape:'url'}">
{/if}
{if $prefs.feature_forums eq 'y' and $prefs.feed_forums eq 'y' and $tiki_p_forum_read eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.feed_forums_title|escape|default:"{tr}RSS Forums{/tr}"}' href="tiki-forums_rss.php?ver={$prefs.feed_default_version|escape:'url'}">
{/if}
{if $prefs.feature_directory eq 'y' and $prefs.feed_directories eq 'y' and $tiki_p_view_directory eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.feed_directories_title|escape|default:"{tr}RSS Directories{/tr}"}' href="tiki-directories_rss.php?ver={$prefs.feed_default_version|escape:'url'}">
{/if}

{if $prefs.feature_calendar eq 'y' and $prefs.feed_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
	<link rel="alternate" type="application/rss+xml" title='{$prefs.feed_calendar_title|escape|default:"{tr}RSS Calendars{/tr}"}' href="tiki-calendars_rss.php?ver={$prefs.feed_default_version|escape:'url'}">
{/if}

{if $prefs.feature_trackers eq 'y' and $prefs.feed_tracker eq 'y'}
	{foreach from=$rsslist_trackers item="tracker"}
		<link rel="alternate" type="application/rss+xml"
			title='{$prefs.feed_tracker_title|cat:" - "|cat:$tracker.name|escape|default:"{tr}RSS Tracker{/tr}"}'
			href="tiki-tracker_rss.php?ver={$prefs.feed_default_version|escape:'url'}&trackerId={$tracker.trackerId}">
	{/foreach}
{/if}

{if ($prefs.feature_blogs eq 'y' and $prefs.feature_blog_sharethis eq 'y') or ($prefs.feature_articles eq 'y' and $prefs.feature_cms_sharethis eq 'y') or ($prefs.feature_wiki eq 'y' and $prefs.feature_wiki_sharethis eq 'y')}
	{if $prefs.blog_sharethis_publisher neq "" and $prefs.article_sharethis_publisher neq ""}
		<script type="text/javascript" src="https://ws.sharethis.com/button/sharethis.js#publisher={$prefs.blog_sharethis_publisher}&amp;type=website&amp;buttonText=&amp;onmouseover=false&amp;send_services=aim"></script>
	{elseif $prefs.blog_sharethis_publisher neq "" and $prefs.article_sharethis_publisher eq ""}
		<script type="text/javascript" src="https://ws.sharethis.com/button/sharethis.js#publisher={$prefs.blog_sharethis_publisher}&amp;type=website&amp;buttonText=&amp;onmouseover=false&amp;send_services=aim"></script>
	{elseif $prefs.blog_sharethis_publisher eq "" and $prefs.article_sharethis_publisher neq ""}
		<script type="text/javascript" src="https://ws.sharethis.com/button/sharethis.js#publisher={$prefs.article_sharethis_publisher}&amp;type=website&amp;buttonText=&amp;onmouseover=false&amp;send_services=aim"></script>
	{elseif $prefs.blog_sharethis_publisher eq "" and $prefs.article_sharethis_publisher eq ""}
		<script type="text/javascript" src="https://ws.sharethis.com/button/sharethis.js#type=website&amp;buttonText=&amp;onmouseover=false&amp;send_services=aim"></script>
	{/if}
{/if}

<!--[if lt IE 9]>{* according to http://remysharp.com/2009/01/07/html5-enabling-script/ *}
	<script src="vendor/afarkas/html5shiv/dist/html5shiv.min.js" type="text/javascript"></script>
<![endif]-->

{if $headerlib}		{$headerlib->output_headers()}{/if}

{if $prefs.feature_custom_html_head_content}
	{eval var=$prefs.feature_custom_html_head_content}
{/if}
{* END of html head content *}
