<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: perform_trade.php 57966 2016-03-17 20:05:33Z jonnybradley $


/**
 * Performs the second half of a trade - "offer mode" only and also cclite only
 * The payment system does the first half ($user to manager account), this does manager to destination
 *
 * Work in progress
 *
 * @param string $main_user
 * @param string $other_user
 * @param float $price
 * @param string $currency
 * @param string $wanted = 'n'
 */
function payment_behavior_perform_trade( $params )
{
	global $prefs, $cclitelib;
	$userlib = TikiLib::lib('user');
	$paymentlib = TikiLib::lib('payment');
	$smarty = TikiLib::lib('smarty');
	require_once 'lib/payment/cclitelib.php';

	$default = array( 'wanted' => 'n', 'registry' => '', 'currency' => '' );
	$params = array_merge($default, $params);

	$smarty->assign('ccresult_ok', false);


	if (!$userlib->user_exists($params['main_user'])) {
		$smarty->assign('ccresult2', "Perform Trade: Main user {$params['main_user']} not found");
	}

	if (!$userlib->user_exists($params['other_user'])) {
		$smarty->assign('ccresult2', "Perform Trade: Other user {$params['other_user']} not found");
	}

	if (!$params['price'] || (int) $params['price'] === 0) {
		$smarty->assign('ccresult2', "Perform Trade: price not set");
	}

	$result = $cclitelib->pay_user(
		$params['price'],
		$params['currency'],
		$params['registry'],
		$params['other_user'],
		$params['main_user']
	);

	if (!empty($result)) {
		$smarty->assign('ccresult2', $result);
		$smarty->assign('ccresult_ok', (strpos($result, 'Transaction Accepted') !== false));
	} else {
		$smarty->assign('ccresult2', tr('Payment was sent but verification is not currently available (this feature is a work in progress)'));
	}
	return $result;
}

