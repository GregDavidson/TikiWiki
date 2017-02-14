<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: svnswitch.php 57973 2016-03-17 20:10:42Z jonnybradley $

require dirname(__FILE__) . '/svntools.php';

if (! isset($_SERVER['argc']) || $_SERVER['argc'] != 2)
	error("Missing argument. Expecting branch to switch to.\n\nExamples:\n\tbranches/3.0\n\tbranches/experimental/foobar");

$branch = full($_SERVER['argv'][1]);
$branch = escapeshellarg($branch);

`svn switch $branch`;
