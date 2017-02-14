<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: display_banner.php 60348 2016-11-21 16:55:47Z jonnybradley $

// Only to be called from edit_banner or view_banner to display the banner without adding
// impressions to the banner
if (!isset($_REQUEST["id"])) {
	die;
}

require_once ('tiki-setup.php');

// CHECK FEATURE BANNERS HERE
$access->check_feature('feature_banners');

$bannerlib = TikiLib::lib('banner');

$data = $bannerlib->get_banner($_REQUEST["id"]);
$id = $data["bannerId"];

switch ($data["which"]) {
	case 'useHTML':
		$raw = $data["HTMLData"];
    	break;

	case 'useImage':
		$raw = "<img src=\"banner_image.php?id=" . $id . "\" />";
    	break;

	case 'useFixedURL':
		$raw = '<img src="'.$data["fixedURLData"].'">"';
    	break;

	case 'useText':
		$raw = $data["textData"];
    	break;
}
print ($raw);
