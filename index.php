<?php
/**
 * This redirects to the site's root to prevent directory browsing.
 * @ignore 
 * @package TikiWiki 
 * @copyright (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
 * @licence Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */
// $Id: index.php 57973 2016-03-17 20:10:42Z jonnybradley $
require_once 'NGender/tiki-ngender.php';
require_once ('check_composer_exists.php');
require_once ('tiki-setup.php');
//error_log(__FILE__ . ', ' . __LINE__ . "Location: '.base_url.prefs['tikiIndex']" . 'Location: '.$base_url.$prefs['tikiIndex']); 
if ( ! headers_sent($header_file, $header_line) ) {
	// rfc2616 wants this to have an absolute URI
	header('Location: '.$base_url.$prefs['tikiIndex']);
} else {
	echo "Header already sent in ".$header_file." at line ".$header_line;
	exit();
}

