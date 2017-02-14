<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-user_reports_send.php 57956 2016-03-17 19:58:12Z jonnybradley $

require_once ('tiki-setup.php');

if (php_sapi_name() != 'cli') {
	$access->check_permission('tiki_p_admin');
}

echo("This script is deprecated and does not work in Multitiki installations.\nPlease use 'console.php daily-report:send' instead.");

$access->check_feature('feature_daily_report_watches');

$reportsManager = Reports_Factory::build('Reports_Manager');
$reportsManager->send();