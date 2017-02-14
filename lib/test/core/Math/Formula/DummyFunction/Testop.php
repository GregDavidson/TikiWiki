<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Testop.php 57963 2016-03-17 20:03:23Z jonnybradley $

class Math_Formula_DummyFunction_Testop extends Math_Formula_Function
{
	function evaluate($element)
	{
		$object = $element->object;
		$concat = $element->concat;

		if ($object && $concat && count($object) == 2 && count($concat) == 1) {
			$type = $this->evaluateChild($object[0]);
			$id = $this->evaluateChild($object[1]);
			$other = $this->evaluateChild($concat[0]);

			return $type . $id . $other;
		} else {
			$this->error('Wrong argument count for testop');
		}
	}
}

