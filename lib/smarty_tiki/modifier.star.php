<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: modifier.star.php 57964 2016-03-17 20:04:05Z jonnybradley $

function smarty_modifier_star($score)
{
	global $prefs, $tikilib;

	if ($prefs['feature_score'] != 'y') {
		return '';
	}

	return $tikilib->get_star($score);
}
