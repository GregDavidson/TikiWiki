<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: WikiContent.php 57968 2016-03-17 20:06:57Z jonnybradley $

class TikiFilter_WikiContent implements Zend\Filter\FilterInterface
{
	function filter( $value )
	{
		$parserlib = TikiLib::lib('parser');
		$noparsed = array();
		$parserlib->plugins_remove($value, $noparsed);

		$value = TikiFilter::get('xss')->filter($value);

		$parserlib->isEditMode = true;
		$parserlib->plugins_replace($value, $noparsed, true);
		$parserlib->isEditMode = false;

		return $value;
	}
}
