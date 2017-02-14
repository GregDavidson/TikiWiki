<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_payment.php 57961 2016-03-17 20:01:56Z jonnybradley $

function wikiplugin_payment_info()
{
	return array(
		'name' => tra('Payment'),
		'documentaion' => 'PluginPayment',
		'description' => tra('Show details of a payment request or invoice'),
		'prefs' => array( 'wikiplugin_payment', 'payment_feature' ),
		'iconname' => 'money',
		'introduced' => 5,
		'params' => array(
			'id' => array(
				'required' => true,
				'name' => tra('Payment Request Number'),
				'description' => tra('Unique identifier of the payment request'),
				'since' => '5.0',
				'filter' => 'digits',
				'default' => '',
			)
		)
	);
}

function wikiplugin_payment( $data, $params )
{
	$smarty = TikiLib::lib('smarty');

	require_once 'lib/smarty_tiki/function.payment.php';
	return '^~np~' . smarty_function_payment($params, $smarty) . '~/np~^';
}
