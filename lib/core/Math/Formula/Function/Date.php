<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Date.php 59780 2016-09-23 08:07:13Z kroky6 $

class Math_Formula_Function_Date extends Math_Formula_Function
{
	function evaluate( $element )
	{
		$elements = array();

		if (count($element) > 2) {
			$this->error(tr('Too many arguments on date.'));
		}

		foreach ( $element as $child ) {
			$elements[] = $this->evaluateChild($child);
		}

		$format = array_shift($elements);
		if (empty($format)) {
			$format = 'U';	// Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)
		}
		$timestamp = intval(array_shift($elements));

		$tikilib = TikiLib::lib('tiki');
		$tz = $tikilib->get_display_timezone();
		$old_tz = date_default_timezone_get();
		if( $tz )
			date_default_timezone_set($tz);

		$date = null;
		if (empty($timestamp)) {
			$date = date($format);
		} else {
			$date = date($format, $timestamp);
		}

		date_default_timezone_set($old_tz);

		return $date;
	}
}

