<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: 20101211_kil_feature_phplayers_tiki.php 57973 2016-03-17 20:10:42Z jonnybradley $

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @param $installer
 */
function upgrade_20101211_kil_feature_phplayers_tiki($installer)
{
	$result = $installer->getOne("SELECT COUNT(*) FROM `tiki_preferences` WHERE `name` = 'feature_phplayers' AND `value` =  'y'");
	if ($result > 0) {
		$installer->query("REPLACE `tiki_preferences` SET `name` = 'feature_cssmenus', `value` = 'y'; DELETE FROM `tiki_preferences` WHERE `name` = 'feature_phplayers';");
	}
}
