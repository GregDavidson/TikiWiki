<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: include_community.php 60598 2016-12-12 09:52:18Z kroky6 $

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

if (isset($_REQUEST['userfeatures'])) {
	check_ticket('admin-inc-community');
}

$smarty->assign('event_graph', TikiLib::events()->getEventGraph());

ask_ticket('admin-inc-community');
