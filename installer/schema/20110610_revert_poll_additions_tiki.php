<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: 20110610_revert_poll_additions_tiki.php 57973 2016-03-17 20:10:42Z jonnybradley $

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @param $installer
 */
function upgrade_20110610_revert_poll_additions_tiki($installer)
{
	$installer->query("DROP TABLE IF EXISTS `tiki_poll_votes`");
	$result = $installer->fetchAll("SHOW COLUMNS FROM `tiki_polls` WHERE `Field`='anonym'");

	if ($result) {
		$result = $installer->query("ALTER TABLE `tiki_polls` DROP COLUMN `anonym`;");
	}
}
