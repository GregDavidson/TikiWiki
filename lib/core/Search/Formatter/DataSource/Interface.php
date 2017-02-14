<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Interface.php 57971 2016-03-17 20:09:05Z jonnybradley $

interface  Search_Formatter_DataSource_Interface
{
	/**
	 * Provides all of the fields in the same group as the requested field for a
	 * given entry.
	 */
	function getData($entry, $field);
}

