<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ResultSet.php 57971 2016-03-17 20:09:05Z jonnybradley $

class Search_Elastic_ResultSet extends Search_ResultSet
{
	function highlight($content)
	{
		if (isset($content['_highlight'])) {
			return strip_tags($content['_highlight'], '<em>');
		}
	}
}

