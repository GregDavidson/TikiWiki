<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: zend.php 59487 2016-08-22 16:45:14Z jonnybradley $

function prefs_zend_list()
{
	return array(
		'zend_mail_smtp_server' => array(
			'name' => tra('SMTP Server'),
			'type' => 'text',
			'size' => '20',
			'perspective' => false,
			'default' => '',
		),
		'zend_mail_smtp_user' => array(
			'name' => tra('Username'),
			'type' => 'text',
			'size' => '20',
			'perspective' => false,
			'parameters' => array(
				'autocomplete' => 'off'
			),
			'default' => '',
		),
		'zend_mail_smtp_pass' => array(
			'name' => tra('Password'),
			'type' => 'password',
			'size' => '20',
			'perspective' => false,
			'parameters' => array(
				'autocomplete' => 'off'
			),
			'default' => '',
		),
		'zend_mail_smtp_port' => array(
			'name' => tra('Port'),
			'type' => 'text',
			'size' => '5',
			'perspective' => false,
			'default' => 25,
		),
		'zend_mail_smtp_security' => array(
			'name' => tra('Security'),
			'type' => 'list',
			'perspective' => false,
			'options' => array(
				'' => tra('None'),
				'ssl' => tra('SSL'),
				'tls' => tra('TLS'),
			),
			'default' => '',
		),
		'zend_mail_handler' => array(
			'name' => tra('Mail sender'),
			'type' => 'list',
			'options' => array(
				'sendmail' => tra('Sendmail'),
				'smtp' => tra('SMTP'),
				'file' => tra('File (debug)'),
			),
			'default' => 'sendmail',
		),
		'zend_mail_smtp_auth' => array(
			'name' => tra('Authentication'),
			'description' => tra('Mail server authentication'),
			'type' => 'list',
			'options' => array(
				'' => tra('None'),
				'login' => tra('LOGIN'),
				'plain' => tra('PLAIN'),
				'crammd5' => tra('CRAM-MD5'),
			),
			'default' => '',
		),
		'zend_mail_smtp_helo' => array(
			'name' => tra('Local server name'),
			'description' => tra('Name of the local server. Will be reported to SMTP relay on the HELO/EHLO line.'),
			'type' => 'text',
			'size' => '20',
			'perspective' => false,
			'default' => 'localhost',
		),
		'zend_mail_queue' => array(
			'name' => tra('Mail Delivery'),
			'description' => tr('When set to Queue, messages will be stored in the database. Requires using the shell script %0 to be run for actual delivery. Only works with SMTP mail.',
								'<code>php console.php mail-queue:send</code>'),
			'type' => 'list',
			'options' => array(
				'' => tra('Send immediately'),
				'y' => tra('Queue'),
			),
			'default' => '',
		),
		'zend_http_sslverifypeer' => array(
			'name' => tra('Verify HTTPS certificates of remote servers'),
			'description' => tra('When set to enforce, the server will fail to connect over HTTPS to a remote server that do not have a SSL certificate that is valid and can be verified against the local list of Certificate Authority (CA)'),
			'type' => 'list',
			'options' => array(
				'' => tra('Do not enforce verification'),
				'y' => tra('Enforce verification'),
			),
			'default' => '',
		),
	);
}
