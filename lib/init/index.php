<?php
/**
 * This redirects to the site's root to prevent directory browsing.
 *
 * @ignore
 * @package TikiWiki
 * @subpackage lib\init
 * @copyright (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
 * @licence Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */
// $Id: index.php 57967 2016-03-17 20:06:16Z jonnybradley $

header("location: ../index.php");
die;
