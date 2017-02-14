<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-featured_link.php 57957 2016-03-17 19:58:54Z jonnybradley $

require_once ('tiki-setup.php');
include_once ('lib/featured_links/flinkslib.php');

$access->check_feature('feature_featuredLinks');

$flinkslib->add_featured_link_hit($_REQUEST["url"]);
// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["url"]) || !$flinkslib->get_featured_link($_REQUEST['url'])) {
	$smarty->assign('msg', tra("No page indicated"));
	$smarty->display("error.tpl");
	die;
}

$section = 'featured_links';
include_once ('tiki-section_options.php');

$smarty->assign_by_ref('url', $_REQUEST["url"]);
$smarty->assign('mid', 'tiki-featured_link.tpl');
$smarty->display("tiki.tpl");
