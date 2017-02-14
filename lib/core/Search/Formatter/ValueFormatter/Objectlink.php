<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Objectlink.php 57970 2016-03-17 20:08:22Z jonnybradley $

class Search_Formatter_ValueFormatter_Objectlink extends Search_Formatter_ValueFormatter_Abstract
{
	function render($name, $value, array $entry)
	{
		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_function_object_link');

		$params = array(
			'type' => $entry['object_type'],
			'id' => $entry['object_id'],
			'title' => $value,
		);

		if (isset($entry['url'])) {
			$params['url'] = $entry['url'];
		}

		return '~np~' . smarty_function_object_link($params, $smarty) . '~/np~';
	}
}

