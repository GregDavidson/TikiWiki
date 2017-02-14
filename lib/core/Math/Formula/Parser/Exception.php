<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Exception.php 57971 2016-03-17 20:09:05Z jonnybradley $

class Math_Formula_Parser_Exception extends Math_Formula_Exception
{
	function __construct( $message, array $tokens, $code = null )
	{
		$message = tr('%0 near "%1"', $message, implode(' ', $tokens));
		parent::__construct($message, $code);
	}
}

