<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Callback.php 57968 2016-03-17 20:06:57Z jonnybradley $

class TikiFilter_Callback implements Zend\Filter\FilterInterface
{
	private $callback;

	function __construct( $callback )
	{
		$this->callback = $callback;
	}

	function filter( $value )
	{
		$f = $this->callback;

		return $f( $value );
	}
}
