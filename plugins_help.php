<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: plugins_help.php 57960 2016-03-17 20:01:11Z jonnybradley $

//this script may only be included - so its better to err & die if called directly.
//smarty is not there - we need setup
require_once('tiki-setup.php');  
$access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));

$wikilib = TikiLib::lib('wiki');
$plugins = $wikilib->list_plugins(true);

$smarty->assign_by_ref('plugins', $plugins);
