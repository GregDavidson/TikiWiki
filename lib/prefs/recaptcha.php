<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: recaptcha.php 60064 2016-10-27 07:33:12Z rjsmelo $

function prefs_recaptcha_list()
{
    return array (
		'recaptcha_enabled' => array(
            'name' => tra('Use ReCaptcha'),
            'description' => tra('Use ReCaptcha, a specialized captcha service, instead of default CAPTCHA'),
			'hint' => tra('You will need to register at [http://www.google.com/recaptcha]'),
			'help' => 'Spam+protection',
			'type' => 'flag',
			'default' => 'n',
        ),
		'recaptcha_pubkey' => array(
			'name' => tra('Site key'),
            'type' => 'text',
            'description' => tra('ReCaptcha public key obtained after registering.'),
        	'size' => 60,
			'default' => '',
		),
		'recaptcha_privkey' => array(
			'name' => tra('Secret key'),
			'type' => 'text',
            'description' => tra('ReCaptcha private key obtained after registering.'),
			'size' => 60,
			'default' => '',
		),
		'recaptcha_theme' => array(
			'name' => tra('ReCaptcha theme'),
			'type' => 'list',
			'description' => tra('Choose a theme for the ReCaptcha widget.'),
			'options' => array(
				'clean' => tra('Clean'),
				'blackglass' => tra('Black Glass'),
				'red' => tra('Red'),
				'white' => tra('White'),
			),
			'default' => 'clean',
		),
		'recaptcha_version' => array(
			'name' => tra('Version'),
			'type' => 'list',
			'description' => tra('ReCaptcha version.'),
			'options' => array(
				'1' => tra('1.0'),
				'2' => tra('2.0'),
			),
			'default' => '2',
		),
	);
}
