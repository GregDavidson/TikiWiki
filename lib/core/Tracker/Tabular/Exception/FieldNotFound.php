<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: FieldNotFound.php 57968 2016-03-17 20:06:57Z jonnybradley $

namespace Tracker\Tabular\Exception;

class FieldNotFound extends Exception
{
	private $field;

	function __construct($field)
	{
		parent::__construct(tr('Field not found: %0', $field));
		$this->field = $field;
	}
}

