<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: include_user.php 60598 2016-12-12 09:52:18Z kroky6 $

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

if (isset($_REQUEST['userfeatures'])) {
	check_ticket('admin-inc-user');
}

$command_parts = [
	realpath(__DIR__ . '/../console.php'),
	'notification:digest',
	$url_host,
	7,
];

if ($url_port) {
	$command_parts[] = '--port=' . $url_port;
}
if ($tikiroot != '/') {
	$command_parts[] = '--path=' . $tikiroot;
}
if ($url_scheme == 'https') {
	$command_parts[] = '--ssl';
}
$command = implode(' ', $command_parts);
$smarty->assign('monitor_command', $command);

ask_ticket('admin-inc-user');
