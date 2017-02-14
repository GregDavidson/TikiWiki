<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: 20110519_quick_edit_categ_params_merge_tiki.php 57973 2016-03-17 20:10:42Z jonnybradley $

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @param $installer
 */
function upgrade_20110519_quick_edit_categ_params_merge_tiki($installer)
{
	$result = $installer->query("select moduleId, params from tiki_modules where name='quick_edit'; ");
	while ($row = $result->fetchRow()) {
		$params = $row['params'];
		if (stripos($params, "addcategId=") === false) {
			$params = preg_replace('/categid=/i', 'addcategId=', $params);
		}
		$installer->query("update tiki_modules set params='" . $params . "' where moduleId=" . $row['moduleId'] . "; ");
	}
}
