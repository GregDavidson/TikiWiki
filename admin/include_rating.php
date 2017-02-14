<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: include_rating.php 57974 2016-03-17 20:11:05Z jonnybradley $

if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

$ratingconfiglib = TikiLib::lib('ratingconfig');
$ratinglib = TikiLib::lib('rating');
$access = TikiLib::lib('access');

if ( isset($_REQUEST['test']) && $access->is_machine_request() ) {
	$message = $ratinglib->test_formula($_REQUEST['test'], array( 'type', 'object-id' ));

	$access->output_serialized(
		array(
			'valid' => empty($message),
			'message' => $message,
		)
	);
	exit;
}

if ( isset($_POST['create']) && ! empty($_POST['name']) ) {
	$id = $ratingconfiglib->create_configuration($_POST['name']);
	$access->flash(tr('New configuration created (id %0)', $id));
}

if ( isset($_POST['edit']) ) {
	$ratingconfiglib->update_configuration($_POST['config'], $_POST['name'], $_POST['expiry'], $_POST['formula']);
	$access->flash(tra('Configuration updated.'));
}

$configurations = $ratingconfiglib->get_configurations();

$smarty->assign('configurations', $configurations);

