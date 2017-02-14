<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: modifier.tiki_short_date.php 57964 2016-03-17 20:04:05Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_modifier_tiki_short_date($string)
{
	global $prefs;
	$smarty = TikiLib::lib('smarty');
	$smarty->loadPlugin('smarty_modifier_tiki_date_format');
	$date = smarty_modifier_tiki_date_format($string, $prefs['short_date_format']);

	if ($prefs['jquery_timeago'] === 'y') {
		TikiLib::lib('header')->add_jq_onready('$("time.timeago").timeago();');
		return '<time class="timeago" datetime="' . TikiLib::date_format('c', $string, false, 5, false) .  '">' . $date . '</time>';
	} else  {
		return $date;
	}
}
