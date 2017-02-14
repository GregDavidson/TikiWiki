<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: antibot.php 57974 2016-03-17 20:11:05Z jonnybradley $

require_once ('tiki-setup.php');
if (!$prefs['feature_antibot'] == 'y') {
	die;
}

$captchalib = TikiLib::lib('captcha');

$captchalib->generate();
$captcha = array('captchaId' => $captchalib->getId(), 'captchaImgPath' => $captchalib->getPath());

echo json_encode($captcha);
