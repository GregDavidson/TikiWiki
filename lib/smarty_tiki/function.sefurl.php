<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.sefurl.php 58538 2016-05-04 15:41:25Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

if (!function_exists('smarty_function_sefurl')) {
	function smarty_function_sefurl($params, $smarty)
	{
		global $prefs;
		$wikilib = TikiLib::lib('wiki');
		$url = '';
	
		// structure only yet
		if (isset($params['structure'])) {
			if ($prefs['feature_sefurl'] != 'y' || (isset($params['sefurl']) && $params['sefurl'] == 'n')) {
				$url = 'tiki-index.php?page=' . urlencode($params['page']) . '&amp;structure=' . urlencode($params['structure']);
			} else {
				$url = $wikilib->sefurl($params['page']);
				$structs = TikiLib::lib('struct')->get_page_structures($params['page']);
				if ($prefs['feature_wiki_open_as_structure'] === 'n' || count($structs) > 1) {
					$url .= (strpos($url, '?') === false ? '?' : '&amp;') . 'structure=' . urlencode($params['structure']);
				}
			}
			if (isset($_REQUEST['no_bl']) && $_REQUEST['no_bl'] === 'y') {
				$url .= (strpos($url, '?') === false ? '?' : '&amp;') . 'latest=1';
			}
		}
		if ($prefs['page_n_times_in_a_structure'] == 'y') {
			$url .= (strpos($url, '?') === false ? '?' : '&amp;') . 'page_ref_id=' . $params['page_ref_id'];
		}
		return $url;
	}
}
