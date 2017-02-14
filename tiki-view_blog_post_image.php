<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-view_blog_post_image.php 57956 2016-03-17 19:58:12Z jonnybradley $

require_once ('tiki-setup.php');

$access->check_feature('feature_blogs');

$bloglib = TikiLib::lib('blog');
if (!isset($_REQUEST['imgId'])) {
	die;
}
$info = $bloglib->get_post_image($_REQUEST['imgId']);
$type = & $info['filetype'];
$file = & $info['filename'];
$content = & $info['data'];
header("Content-type: $type");
header("Content-Disposition: inline; filename=$file");
echo $content;
