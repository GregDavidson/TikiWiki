<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Local.php 57968 2016-03-17 20:06:57Z jonnybradley $

class Tiki_Profile_Transport_Local implements Tiki_Profile_Transport_Interface
{
	function getPageContent($pageName)
	{
		$tikilib = TikiLib::lib('tiki');
		$info = $tikilib->get_page_info($pageName);
		if (empty($info)) {
			return null;
		}
		return $info['data'];
	}

	function getPageParsed($pageName)
	{
		$content = $this->getPageContent($pageName);

		if ($content) {
			return TikiLib::lib('parser')->parse_data($content);
		}
	}
}

