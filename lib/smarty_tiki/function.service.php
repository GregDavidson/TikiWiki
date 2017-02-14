<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.service.php 57964 2016-03-17 20:04:05Z jonnybradley $

function smarty_function_service($params, $smarty)
{
	$servicelib = TikiLib::lib('service');
	$smarty->loadPlugin('smarty_modifier_escape');

	if (! isset($params['controller'])) {
		return 'missing-controller';
	}

	if (isset($params['_params'])) {
		$params += $params['_params'];
		unset($params['_params']);
	}

	$url = $servicelib->getUrl($params);
	return smarty_modifier_escape($url);
}

