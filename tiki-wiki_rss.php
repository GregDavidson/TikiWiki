<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-wiki_rss.php 57956 2016-03-17 19:58:12Z jonnybradley $

require_once ('tiki-setup.php');
$tikilib = TikiLib::lib('tiki');
$histlib = TikiLib::lib('hist');
$wikilib = TikiLib::lib('wiki');
$rsslib = TikiLib::lib('rss');

$access->check_feature('feature_wiki');

if ($prefs['feed_wiki'] != 'y') {
	$errmsg=tra("rss feed disabled");
	require_once ('tiki-rss_error.php');
}

$res=$access->authorize_rss(array('tiki_p_view', 'tiki_p_wiki_view_ref'));
if ($res) {
	if ($res['header'] == 'y') {
		header('WWW-Authenticate: Basic realm="'.$tikidomain.'"');
		header('HTTP/1.0 401 Unauthorized');
	}
	$errmsg=$res['msg'];
	require_once ('tiki-rss_error.php');
}

$feed = "wiki";
$uniqueid = $feed;
$output = $rsslib->get_from_cache($uniqueid);

if ($output["data"]=="EMPTY") {
	$title = $prefs['feed_wiki_title'];
	$desc = $prefs['feed_wiki_desc'];
	$id = "pageName";
	$titleId = "pageName";
	$descId = "data";
	$dateId = "lastModif";
	$authorId = "user";
	// if param &diff=1 exists, link to diff, not to page itself
	if (isset($_REQUEST["diff"])) {
		$readrepl = "tiki-pagehistory.php?page=%s&compare=1&oldver=%s&newver=0&diff_style=minsidediff";
	} else {
		$readrepl = "tiki-index.php?page=%s";
	}
	$param = "previous";

	$changes = $tikilib -> list_pages(0, $prefs['feed_wiki_max'], 'lastModif_desc', '', '', true, false, false, false, '', false, 'y');
	$tmp = array();
	foreach ($changes["data"] as $data) {
		$result = '';
		if ($tiki_p_view != 'y') {
			$data['sefurl'] = $wikilib->sefurl($data['pageName']);
			unset($data['data']);
			$tmp[] = $data;
			continue;
		}
		// get last 2 versions of the page and parse them
		$curr_page = $tikilib->get_page_info($data["pageName"]);
		$pageversion = (int) $histlib->get_page_latest_version($data["pageName"]);
		if ($pageversion==false) {
			$prev_page = $curr_page;
			$prev_page["data"]="";
		} else {
			$prev_page = $histlib->get_page_from_history($data["pageName"], $pageversion, true);
		}
		$_REQUEST['redirectpage'] = 'y';//block the redirect interpretation
		$_REQUEST['page'] = $data["pageName"];
		$curr_page_p = $tikilib->parse_data($curr_page[$descId], array('print' => true, 'is_html' => $curr_page['is_html']));
		$prev_page_p = $tikilib->parse_data($prev_page[$descId], array('print' => true, 'is_html' => $curr_page['is_html']));

		// do a diff between both pages
		require_once('lib/diff/difflib.php');
		$diff = diff2($prev_page_p, $curr_page_p, $curr_page['is_html'] ? 'htmldiff' : 'unidiff');


		if (is_array($diff)) {
			foreach ($diff as $part) {
				if ($part["type"] == "diffdeleted") {
					foreach ($part["data"] as $chunk) {
						$result .= "<blockquote>- $chunk</blockquote>";
					}
				}
				if ($part["type"] == "diffadded") {
					foreach ($part["data"] as $chunk) {
						$result .= "<blockquote>+ $chunk</blockquote>";
					}
				}
			}
		} else {
			$result = strpos($diff, '<tr>') === 0 ? '<table>' . $diff . '</table>' : $diff;
		}

		$data["$descId"] = $result;

		// hand over the version of the second page
		$data["$param"] = $prev_page["version"];
		$tmp[] = $data;
	}
	$changes["data"] = $tmp;

	$tmp = null;
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, $param, $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
}
header("Content-type: ".$output["content-type"]);
print $output["data"];
