<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: 20110115_create_bottom_modules_tiki.php 57973 2016-03-17 20:10:42Z jonnybradley $

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/*
 * Prefs replaced (and removed) by this update:
 * 	feature_site_report
 *  feature_site_send_link
 *  feature_tell_a_friend
 *  feature_bot_bar_power_by_tw
 *  feature_topbar_version
 *  feature_bot_bar_icons
 *  feature_bot_bar_rss
 *  feature_babelfish
 *  feature_babelfish_logo (TODO - still needs lib/setup/babelfish.php for this to work and i can't work out why)
 *  feature_bot_bar_debug
 *  feature_bot_bar
 */

/**
 * @param $installer
 */
function upgrade_20110115_create_bottom_modules_tiki($installer)
{

	$prefs = array();
	$result = $installer->table('tiki_preferences')->fetchAll(array('name', 'value'), array());
	foreach ($result as $res) {
		$prefs[$res['name']] = $res['value'];
	}

	// merge in relevant defaults from 6.x as they are no longer defined in 7.x+
	$prefs = array_merge(
		array(
			'feature_site_report' => 'n',
			'feature_site_send_link' => 'n',
			'feature_tell_a_friend' => 'n',
			'feature_share' => 'n',
			'feature_bot_bar_power_by_tw' => 'y',
			'feature_bot_bar_icons' => 'n',
			'feature_topbar_version' => 'n',
			'feature_bot_bar_rss' => 'y',
			'feature_babelfish' => 'n',
			'feature_babelfish_logo' => 'n',
			'feature_bot_bar_debug' => 'n',
		),
		$prefs
	);

	// add site report
	if ($prefs['feature_site_report'] === 'y' || ($prefs['feature_site_send_link'] === 'y' && $prefs['feature_tell_a_friend'] === 'y')) {
		$params = '';
		$params .= $prefs['feature_site_report'] !== 'y'	? '&report=n' : '';
		$params .= $prefs['feature_share'] !== 'y'		 	? '&share=n' : '';
		$params .= $prefs['feature_site_send_link'] !== 'y'	? '&email=n' : '';

		$installer->query(
			"INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES " .
			"('share','b',1,7200,'nobox=y$params','a:0:{}');"
		);
	}

	// add poweredby
	if ($prefs['feature_bot_bar_power_by_tw'] !== 'n' || $prefs['feature_bot_bar_icons'] === 'y') {
		$params = '';
		$params .= $prefs['feature_bot_bar_power_by_tw'] !== 'y'	? '&tiki=n' : '';
		$params .= $prefs['feature_bot_bar_icons'] !== 'y'		 	? '&icons=n' : '';
		$params .= $prefs['feature_topbar_version'] !== 'y'			? '&version=n' : '';

		$installer->query(
			"INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES " .
			"('poweredby','b',2,7200,'nobox=y$params','a:0:{}');"
		);
	}

	// add rsslist
	if ($prefs['feature_bot_bar_rss'] !== 'n') {
		$installer->query(
			"INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES " .
			"('rsslist','b',3,7200,'nobox=y','a:0:{}');"
		);
	}

	// add babelfish list
	if ($prefs['feature_babelfish'] === 'y') {
		$installer->query(
			"INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES " .
			"('babelfish_links','b',5,7200,'nobox=y&style=text-align%3Aleft%3B','a:0:{}');"
		);
	}

	// add babelfish logo
	if ($prefs['feature_babelfish_logo'] === 'y') {
		$installer->query(
			"INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES " .
			"('babelfish_logo','b',4,7200,'nobox=y&style=float%3Aright%3B','a:0:{}');"
		);
	}

	// add loadstats
	if ($prefs['feature_bot_bar_debug'] === 'y') {
		$installer->query(
			"INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES " .
			"('loadstats','b',6,0,'nobox=y','a:1:{i:0;s:6:\"Admins\";}');"
		);
	}

	// add svnup
	if (is_dir('.svn')) {
		$installer->query(
			"INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES " .
			"('svnup','b',7,0,'nobox=y','a:1:{i:0;s:6:\"Admins\";}');"
		);
	}

	//	TODO uncomment when stable (pre Tiki 7 release)
	//	$installer->query("DELETE FROM `tiki_preferences` WHERE `name` IN ".
	//							"('feature_site_report','feature_site_send_link','feature_tell_a_friend','feature_bot_bar_power_by_tw','feature_topbar_version',
	//                            'feature_bot_bar_icons','feature_bot_bar_rss','feature_babelfish','feature_babelfish_logo','feature_bot_bar_debug');");


}
