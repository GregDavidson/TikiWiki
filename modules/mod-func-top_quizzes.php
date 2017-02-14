<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-top_quizzes.php 57960 2016-03-17 20:01:11Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_top_quizzes_info()
{
	return array(
		'name' => tra('Top Quizzes'),
		'description' => tra('Displays the specified number of quizzes with links to them, starting with the one having the most hits.'),
		'prefs' => array('feature_quizzes'),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_top_quizzes($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');
	$quizlib = TikiLib::lib('quiz');

	$ranking = $quizlib->list_quiz_sum_stats(0, $mod_reference["rows"], 'timesTaken_desc', '');
	$smarty->assign('modTopQuizzes', $ranking["data"]);
}
