<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Direct.php 60381 2016-11-23 10:56:42Z jonnybradley $

class Search_Type_Factory_Direct implements Search_Type_Factory_Interface
{
	function plaintext($value)
	{
		return new Search_Type_Whole($value);
	}

	function wikitext($value)
	{
		return new Search_Type_PlainText($value);
	}

	function timestamp($value)
	{
		return new Search_Type_Whole($value);
	}

	function identifier($value)
	{
		return new Search_Type_Whole($value);
	}

	function numeric($value)
	{
		return new Search_Type_Numeric($value);
	}

	function multivalue($values)
	{
		return new Search_Type_Whole((array) $values);
	}

	function object($values)
	{
		return new Search_Type_Object($values);
	}

	function nested($values)
	{
		return new Search_Type_Nested($values);
	}

	function geopoint($values)
	{
		return new Search_Type_GeoPoint($values);
	}

	function sortable($value)
	{
		return new Search_Type_Whole($value);
	}
}

