<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.filegal_manager_url.php 57965 2016-03-17 20:04:49Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * filegal_manager_url: Return the URL of the filegal manager, that goes to the list of filegalleries
 */
function smarty_function_filegal_manager_url($params, $smarty)
{
	global $tikilib, $prefs;

	$return =  'tiki-upload_file.php?galleryId='.$prefs['home_file_gallery'].'&view=browse';

	if ( ! empty($params['area_id']) ) {
		$return .= '&filegals_manager=' . $params['area_id'];
	}

	return $return;
}
