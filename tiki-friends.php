<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-friends.php 57957 2016-03-17 19:58:54Z jonnybradley $

require_once('tiki-setup.php');

$access->check_user($user);
$access->check_feature('feature_friends');

$smarty->display("tiki-friends.tpl");
