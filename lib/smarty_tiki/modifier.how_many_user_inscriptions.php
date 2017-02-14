<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: modifier.how_many_user_inscriptions.php 57964 2016-03-17 20:04:05Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     lcfirst
 * Purpose:  to use with the tracker field type "User inscription"
 *           if $text="12[13], 14[15], 16[17]"
 *           then return 48 (=13+1+15+1+17+1)
 * -------------------------------------------------------------
 */
function smarty_modifier_how_many_user_inscriptions( $text )
{

	$pattern = "/\d+\[(\d+)\]/";
	$out = preg_match_all($pattern, $text, $match);

	$nb = 0;

	foreach ($match[1] as $n) {
		$nb += ($n+1);
	}

	return $nb;
}
