<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: NotEquals.php 57971 2016-03-17 20:09:05Z jonnybradley $

class Math_Formula_Function_NotEquals extends Math_Formula_Function
{
	function evaluate( $element )
	{
		// Multiple components will all need to be equal.

		$out = array();

		$reference = $this->evaluateChild($element[0]);

		$count = 0;
		foreach ( $element as $child ) {
			$component = $this->evaluateChild($child);
			if ($component == $reference) {
				if (++$count > 1) {
					return false;
				}
			}
		}

		return true;
	}
}

