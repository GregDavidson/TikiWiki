<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: MultivalueText.php 57970 2016-03-17 20:08:22Z jonnybradley $

class Search_Type_MultivalueText implements Search_Type_Interface
{
	private $values;

	function __construct(array $values)
	{
		$this->values = $values;
	}

	function getRawValue()
	{
		return $this->values;
	}

	function getValue()
	{
		$strings = array();
		foreach ($this->values as $val) {
			$val = md5($val);
			$raw = 'token' . $val;

			// Must strip numbers to prevent tokenization
			$strings[] = strtr($raw, '1234567890', 'pqrtuvwxyz');
		}

		return implode(' ', $strings);
	}
}

