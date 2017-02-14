<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-file_galleries_rss.php 57957 2016-03-17 19:58:54Z jonnybradley $

require_once ('tiki-setup.php');
$rsslib = TikiLib::lib('rss');

$access->check_feature('feature_file_galleries');

if ($prefs['feed_file_galleries'] != 'y') {
        $errmsg=tra("rss feed disabled");
        require_once ('tiki-rss_error.php');
}

$filegallib = TikiLib::lib('filegal');

$feed = "filegals";
$uniqueid = $feed;
$output = $rsslib->get_from_cache($uniqueid);

if ($output["data"]=="EMPTY") {
	$title = $prefs['feed_file_galleries_title'];
	$desc = $prefs['feed_file_galleries_desc'];
	$id = "fileId";
	$descId = "description";
	$dateId = "lastModif";
	$authorId = "lastModifUser";
	$titleId = "filename";
	$readrepl = "tiki-download_file.php?$id=%s";

	$changes = $filegallib->list_files(0, $prefs['feed_file_galleries_max'], $dateId.'_desc', '');
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
}
header("Content-type: ".$output["content-type"]);
print $output["data"];
