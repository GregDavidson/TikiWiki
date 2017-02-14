<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: DateTime.php 57970 2016-03-17 20:08:22Z jonnybradley $

class Search_Type_DateTime implements Search_Type_Interface
{
	private $value;

	function __construct($value)
	{
		if (is_numeric($value)) {
			$this->value = gmdate(DateTime::W3C, $value);
		}
	}

	function getValue()
	{
		return $this->value;
	}
}
