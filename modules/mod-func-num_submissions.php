<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-num_submissions.php 57960 2016-03-17 20:01:11Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_num_submissions_info()
{
	return array(
		'name' => tra('Pending Article Submissions'),
		'description' => tra('Displays the number of article submissions waiting examination and a link to the list.'),
		'prefs' => array('feature_submissions'),
		'params' => array()
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_num_submissions($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');
	$artlib = TikiLib::lib('art');
	$ranking = $artlib->list_submissions(0, -1, 'created_desc', '', '');
	$smarty->assign('modNumSubmissions', $ranking["cant"]);
}
