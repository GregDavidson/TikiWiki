<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: block.itemfield.php 57965 2016-03-17 20:04:49Z jonnybradley $

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_itemfield($params, $content, $smarty, &$repeat)
{
	include_once('lib/wiki-plugins/wikiplugin_trackeritemfield.php');
	if (!$repeat) // only on closing tag
		if (($res = wikiplugin_trackeritemfield($content, $params))!== false)
			echo $res;
}
