<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: categorize.php 60201 2016-11-08 09:52:00Z kroky6 $

require_once('tiki-setup.php');
$access = TikiLib::lib('access');
$access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));
$smarty = TikiLib::lib('smarty');

global $prefs;

// NGender: How can we have this take Stewards into consideration??
$catobjperms = Perms::get(array( 'type' => $cat_type, 'object' => $cat_objid ));
var_log(
				$catobjperms->modify_object_categories, 
				'$catobjperms->modify_object_categories',
				__FILE__, __LINE__ );
// if ($prefs['feature_categories'] == 'y' && $catobjperms->modify_object_categories ) {
// NGender: begin kludge!!
if ( $prefs['feature_categories'] == 'y' ) {
		if ( ! isset( $cat_object_exists ) ) {
			$cat_object_exists = ($cat_objid === 'null') ? false : (bool) $cat_objid;
		}
		$userlib = TikiLib::lib('user');
		$user_is_steward = $cat_object_exists
			? $userlib->is_steward_of($cat_objid)
			: $userlib->user_is_in_group($user, 'Stewards');

	if ( $user_is_steward || $catobjperms->modify_object_categories ) {
			// NGender: end kludge!!
			$categlib = TikiLib::lib('categ');
			
			if (isset($_REQUEST['import']) and isset($_REQUEST['categories'])) {
				$_REQUEST["cat_categories"] = explode(',', $_REQUEST['categories']);
				$_REQUEST["cat_categorize"] = 'on';
			}
			var_log(
							isset($_REQUEST["cat_categorize"]),
							'isset($_REQUEST["cat_categorize"])',
							__FILE__, __LINE__ );
			var_log(
							$_REQUEST["cat_categorize"] != 'on',
							'$_REQUEST["cat_categorize"] != "on"',
							__FILE__, __LINE__ );
			if ( !isset($_REQUEST["cat_categorize"]) || $_REQUEST["cat_categorize"] != 'on' ) {
				$_REQUEST['cat_categories'] = NULL;
			}
			$categlib->update_object_categories(isset($_REQUEST['cat_categories'])?$_REQUEST['cat_categories']:'', $cat_objid, $cat_type, $cat_desc, $cat_name, $cat_href, $_REQUEST['cat_managed'], $userlib->is_steward_of($cat_objid));
		}
	}
