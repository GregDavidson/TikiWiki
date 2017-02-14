<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: http.php 59864 2016-10-01 16:34:58Z rjsmelo $

function prefs_http_list()
{
	return array(
		'http_port' => array(
			'name' => tra('HTTP port'),
            'description' => tra('The port used to access this server; if not specified, port 80 will be used'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
			'default' => '',
			'shorthint' => tra('If not specified, port 80 will be used'),
		),
		'http_skip_frameset' => array(
			'name' => tra('HTTP lookup: skip framesets'),
			'description' => tra('When performing an HTTP request to an external source, verify if the result is a frameset and use heuristic to provide the real content.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'http_referer_registration_check' => array(
			'name' => tra('Registration referrer check'),
			'description' => tra('Use the HTTP referrer to check registration POST is sent from same host. (May not work on some setups.)'),
			'type' => 'flag',
			'default' => 'y',
		),
        'http_header_frame_options' => array(
            'name' => tra('HTTP Header X-Frame Options'),
            'description' => tra('The X-Frame-Options HTTP response header can be used to indicate whether or not a browser should be allowed to render a page in a &lt;frame&gt;, &lt;iframe&gt; or &lt;object&gt;'),
            'type' => 'flag',
            'default' => 'n',
            'perspective' => false,
            'tags' => array('basic'),
        ),
        'http_header_frame_options_value' => array(
            'name' => tra('Header Value'),
            'type' => 'list',
            'options' => array(
                'DENY' => tra('DENY'),
                'SAMEORIGIN' => tra('SAMEORIGIN'),
            ),
            'default' => 'DENY',
            'perspective' => false,
            'tags' => array('basic'),
            'dependencies' => array(
                'http_header_frame_options',
            ),
        ),
        'http_header_xss_protection' => array(
            'name' => tra('HTTP Header X-XSS-Protection'),
            'description' => tra('The x-xss-protection header is designed to enable the cross-site scripting (XSS) filter built into modern web browsers'),
            'type' => 'flag',
            'default' => 'n',
            'perspective' => false,
            'tags' => array('basic'),
        ),
        'http_header_xss_protection_value' => array(
            'name' => tra('Header Value'),
            'type' => 'list',
            'options' => array(
                '0' => tra('0'),
                '1' => tra('1'),
                '1;mode:block' => tra('1;mode:block'),
            ),
            'default' => '1;mode:block',
            'perspective' => false,
            'tags' => array('basic'),
            'dependencies' => array(
                'http_header_xss_protection',
            ),
        ),
	);
}
