<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-layout_options.php 57957 2016-03-17 19:58:54Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
$section_left_column = $section . '_left_column';
$section_right_column = $section . '_right_column';
$smarty->assign('feature_left_column', $$section_left_column);
$smarty->assign('feature_right_column', $$section_right_column);
