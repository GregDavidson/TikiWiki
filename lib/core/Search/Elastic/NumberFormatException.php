<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: NumberFormatException.php 57971 2016-03-17 20:09:05Z jonnybradley $

class Search_Elastic_NumberFormatException extends Search_Elastic_Exception
{
	private $field;
	private $string;

	function __construct($string, $field)
	{
		$this->string = $string;
		$this->field = $field;
		parent::__construct(tr('String "%0" cannot be formatted as a number for field "%1"', $this->string, $this->field));
	}
}

