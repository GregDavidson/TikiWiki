<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-show_user_avatar.php 57956 2016-03-17 19:58:12Z jonnybradley $

require 'tiki-setup.php';

$access->check_feature('feature_userPreferences');

global $tikidomain;
$userprefslib = TikiLib::lib('userprefs');
// application to display an image from the database with
// option to resize the image dynamically creating a thumbnail on the fly.
// you have to check if the user has permission to see this gallery
if (!isset($_REQUEST["user"])) {
	die;
}

if (isset($_REQUEST['fullsize']) && $_REQUEST['fullsize'] == 'y' && $prefs["user_store_file_gallery_picture"] == 'y' && $user_picture_id = $userprefslib->get_user_picture_id($_REQUEST["user"]) ) {
	header('Location: tiki-download_file.php?fileId=' . $user_picture_id . '&amp;display=y');
	die;
}

$info = $userprefslib->get_user_avatar_img($_REQUEST["user"]);
$type = $info["avatarFileType"];
$content = $info["avatarData"];
if (empty($content)) {
	if($prefs['user_default_picture_id']){
		header('Location: tiki-download_file.php?fileId=' . $prefs['user_default_picture_id'] . '&amp;display=y');
	die;
	} else {
		$content = file_get_contents('img/noavatar.png');
	}
}
header("Content-type: $type");
echo $content;

