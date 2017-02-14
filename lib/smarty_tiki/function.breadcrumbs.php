<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.breadcrumbs.php 57965 2016-03-17 20:04:49Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_breadcrumbs($params, $smarty)
{
	global $prefs;
	extract($params);

	if (empty($crumbs)) {
		trigger_error("assign: missing 'crumbs' parameter");
		return;
	}
	if (empty($loc)) {
		trigger_error("assign: missing 'loc' parameter");
		return;
	}
	if ($type === 'pagetitle' && $prefs['site_title_breadcrumb'] === 'y') {
		$type = 'desc';
	}
	$showLinks = empty($params['showLinks']) || $params['showLinks'] == 'y';
	$text_to_display = '';
	switch ($type) {
		case 'invertfull':
			$text_to_display = breadcrumb_buildHeadTitle(array_reverse($crumbs));
			break;
		case 'fulltrail':
			$text_to_display = breadcrumb_buildHeadTitle($crumbs);
			break;
		case 'pagetitle':
			$text_to_display = breadcrumb_getTitle($crumbs, $loc);
			break;
		case 'desc':
			$text_to_display = breadcrumb_getDescription($crumbs, $loc);
			break;
		case 'trail':
		default:
			$text_to_display = breadcrumb_buildTrail($crumbs, $loc, $showLinks);
			break;
    }

    return $text_to_display;
}
