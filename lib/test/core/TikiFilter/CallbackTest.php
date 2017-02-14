<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: CallbackTest.php 57963 2016-03-17 20:03:23Z jonnybradley $

/** 
 * @group unit
 * 
 */

class TikiFilter_CallbackTest extends TikiTestCase
{
	function testSimple()
	{
		$filter = new TikiFilter_Callback('strtoupper');

		$this->assertEquals('HELLO', $filter->filter('hello'));
	}
}
