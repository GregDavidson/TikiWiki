<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-timestamp.php 57956 2016-03-17 19:58:12Z jonnybradley $

require_once ('tiki-setup.php');
require_once("lib/phpseclib_tiki/tikisecure.php");

global $prefs;

if ($prefs['feature_futurelinkprotocol'] == "y") {
	$requester = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	
	if (isset($requester)) {
		if (isset($_REQUEST['hash'], $_REQUEST["clienttime"])) {
			$timestamp = TikiSecure::timestamp($_REQUEST["hash"], $_REQUEST["clienttime"], $requester);
			
		} else if ($_REQUEST['timestamp']) {
			$timestamp = TikiSecure::openTimestamp(json_decode(urldecode($_REQUEST['timestamp'])), $requester);
			
		}
		
		echo json_encode($timestamp);
		exit();
	}
	
	//We list all public keys here
}