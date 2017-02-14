<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Min.php 57971 2016-03-17 20:09:05Z jonnybradley $

class Math_Formula_Function_Min extends Math_Formula_Function
{
	function evaluate( $element )
	{
		$out = $this->evaluateChild($element[0]);

		foreach ( $element as $child ) {
			$out = min($out, $this->evaluateChild($child));
		}

		return $out;
	}
}

