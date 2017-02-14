<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-quiz_download_answer.php 57956 2016-03-17 19:58:12Z jonnybradley $

require_once ('tiki-setup.php');

$access->check_feature('feature_quizzes');

$quizlib = TikiLib::lib('quiz');
if (isset($_REQUEST['answerUploadId'])) {
	$quizlib->download_answer($_REQUEST['answerUploadId']);
}
