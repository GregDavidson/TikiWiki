<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: list-file_backlinks_ajax.php 57961 2016-03-17 20:01:56Z jonnybradley $

require_once('tiki-setup.php');
if ( $prefs['feature_file_galleries'] != 'y' || $prefs['feature_jquery'] != 'y' || $prefs['feature_jquery_autocomplete'] != 'y') {
	/* echo '{}'; */
	exit;
}
$filegallib = TikiLib::lib('filegal');
if (empty($_REQUEST['fileId'])) {
	/* echo '{}'; */
	exit;
}
$info = $filegallib->get_file($_REQUEST['fileId']);
if (empty($info)) {
	/* echo '{}'; */
	exit;
}
$perms = Perms::get(array('type'=>'file gallery', 'object'=>$info['galleryId']));
if (!$perms->list_file_gallery) {
	/* echo '{}'; */
	exit;
}
$backlinks = $filegallib->getFileBacklinks($_REQUEST['fileId']);
$smarty->assign_by_ref('backlinks', $backlinks);
echo $smarty->fetch('file_backlinks.tpl'); 
/*
header( 'Content-Type: application/json' );
echo json_encode( $backlinks );
*/
