<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: exportlib.php 57962 2016-03-17 20:02:39Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

class ExportLib extends TikiLib
{

	function MakeWikiZip()
	{
		global $tikidomain;
		$zipname = 'wikidb.zip';
		include_once ('lib/tar.class.php');
		$tar = new tar();
		$query = 'select `pageName` from `tiki_pages` order by ' . $this->convertSortMode('pageName_asc');
		$result = $this->query($query, array());

		while ($res = $result->fetchRow()) {
			$page = $res['pageName'];
			$content = $this->export_wiki_page($page, 0);
			$tar->addData($page, $content, $this->now);
		}
		$dump = 'dump';

		if ($tikidomain) {
			$dump.= "/$tikidomain";
		}

		$tar->toTar("$dump/export.tar", FALSE);

		return '';
	}

	function export_wiki_page($pageName, $nversions = 1, $showLatest = false)
	{
		global $prefs;

		$head = '';
		$head .= 'Date: ' . $this->date_format('%a, %e %b %Y %H:%M:%S %O'). "\r\n";
		$head .= sprintf("Mime-Version: 1.0 (Produced by Tiki)\r\n");
		$info = $this->get_page_info($pageName);

		if ($prefs['flaggedrev_approval'] == 'y') {
			$flaggedrevisionlib = TikiLib::lib('flaggedrevision');
			if (! $showLatest && $flaggedrevisionlib->page_requires_approval($pageName)) {
				$data = $flaggedrevisionlib->get_version_with($pageName, 'moderation', 'OK');
				$info['data'] = '';
				if ($data) {
					$info['data'] = $data['data'];
				}
			}
		}

		$parts = array();
		$parts[] = MimeifyPageRevision($info);

		if ($nversions > 1 || $nversions == 0) {
			$iter = $this->get_page_history($pageName);
			foreach ($iter as $revision) {
				$parts[] = MimeifyPageRevision($revision);

				if ($nversions > 0 && count($parts) >= $nversions)
					break;
			}
		}
		if (count($parts) > 1)
			return $head . MimeMultipart($parts);

		assert($parts);
		return $head . $parts[0];
	}

	// Returns all the versions for this page
	// without the data itself
	function get_page_history($page)
	{
		$query = 'select `pageName`, `description`, `version`, `lastModif`, `user`, `ip`, `data`, `comment`' .
						' from `tiki_history` where `pageName`=? order by ' . $this->convertSortMode('version_desc');
		$result = $this->query($query, array($page));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux = array();
			$aux['version'] = $res['version'];
			$aux['lastModif'] = $res['lastModif'];
			$aux['user'] = $res['user'];
			$aux['ip'] = $res['ip'];
			$aux['data'] = $res['data'];
			$aux['pageName'] = $res['pageName'];
			$aux['description'] = $res['description'];
			$aux['comment'] = $res['comment'];
			//$aux["percent"] = levenshtein($res["data"],$actual);
			$ret[] = $aux;
		}
		return $ret;
	}
}
$exportlib = new ExportLib;
