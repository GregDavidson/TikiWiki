<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-switch_perspective.php 59610 2016-09-07 07:08:42Z kroky6 $

require_once 'tiki-setup.php';
$perspectivelib = TikiLib::lib('perspective');

$access->check_feature('feature_perspective');

unset($_SESSION['current_perspective']);
unset($_SESSION['current_perspective_name']);

if ( isset($_REQUEST['perspective']) ) {
	$perspectivelib->set_perspective($_REQUEST['perspective']);
}

if ( isset($_REQUEST['back']) && isset($_SERVER['HTTP_REFERER']) ) {
	$access->redirect($_SERVER['HTTP_REFERER']);
} else {
	$access->redirect('index.php');
}

// EOF
