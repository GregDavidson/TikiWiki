<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_sharethis.php 57961 2016-03-17 20:01:56Z jonnybradley $

function wikiplugin_sharethis_info()
{
	return array(
		'name' => tra('ShareThis'),
		'documentation' => 'PluginSharethis',
		'description' => tra('Add a ShareThis button'),
		'prefs' => array( 'wikiplugin_sharethis' ),
		'iconname' => 'sharethis',
		'introduced' => 3,
		'tags' => array( 'basic' ),
		'params' => array(
			'sendsvcs' => array(
				'required' => false,
				'name' => tra('Send Services'),
				'description' => tr('By default, %0email%1, %0aim%1 and %0sms%1 are available. Input one or two of the
					services separated by a %0|%1 to limit the choice of send services.', '<code>', '</code>'),
				'since' => '3.2',
				'default' => '',
				'advanced' => true,
				'separator' => '|',
				'filter' => 'word',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('AIM'), 'value' => 'aim'),
					array('text' => tra('AIM and Email'), 'value' => 'aim|email'),
					array('text' => tra('AIM and SMS'), 'value' => 'aim|sms'),
					array('text' => tra('Email'), 'value' => 'email'),
					array('text' => tra('Email and SMS'), 'value' => 'email|sms'),
				)
			),
			'style' => array(
				'required' => false,
				'name' => tra('Button Style'),
				'description' => tra('Set button style.'),
				'since' => '5.0',
				'default' => '',
				'filter' => 'word',
				'options' => array(
					array('text' => tra(''), 'value' => ''),
					array('text' => tra('Horizontal'), 'value' => 'horizontal'),
					array('text' => tra('Vertical'), 'value' => 'vertical'),
					array('text' => tra('Rotate'), 'value' => 'rotate')
					)
			),
			'rotateimage' => array(
				'required' => false,
				'name' => tra('Rotate Image'),
				'description' => tr('A value of %0y%1 (Yes) will cause the button icon to rotate every 3 seconds between
					a few icons, cycling through twice before stopping.', '<code>', '</code>'),
				'since' => '3.2',
				'default' => '',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'multiple' => array(
				'required' => false,
				'name' => tra('Multiple Icons'),
				'description' => tr('Enter pipe-separated list of services, e.g., %0.',
					'<code>email|facebook|twitter|sharethis</code>'),
				'since' => '5.0',
				'separator' => '|',
				'filter' => 'word',
				'default' => '',
			),
			'postfirst' => array(
				'required' => false,
				'name' => tra('First Services Shown'),
				'description' => tr('Input a list of post services (like %0Facebook|Myspace|Digg%1, etc.) separated by a
					%0|%1 to customize the services that are shown in the opening panel of the widget.', '<code>', '</code>'),
				'since' => '3.2',
				'separator' => '|',
				'filter' => 'word',
				'advanced' => true,
				'default' => '',
			),
			'buttontext' => array(
				'required' => false,
				'name' => tra('Button Text'),
				'description' => tra('Custom link text for the button.'),
				'since' => '3.2',
				'filter' => 'text',
				'default' => '',
			),
			'headertitle' => array(
				'required' => false,
				'name' => tra('Header Title'),
				'description' => tra('Optional header title text for the widget.'),
				'since' => '3.2',
				'filter' => 'text',
				'default' => '',
			),
			'headerbg' => array(
				'required' => false,
				'name' => tra('Header Background'),
				'description' => tra('HTML color code (not color name) for the background color for the header if an
					optional header title is used.'),
				'since' => '3.0',
				'advanced' => true,
				'filter' => 'text',
				'default' => '',
			),
			'headertxtcolor' => array(
				'required' => false,
				'name' => tra('Header Text Color'),
				'description' => tra('HTML color code (not color name) for the header text if an optional header title is used.'),
				'since' => '3.2',
				'advanced' => true,
				'filter' => 'text',
				'default' => '',
			),
			'linkfg' => array(
				'required' => false,
				'name' => tra('Link text color for services'),
				'description' => tra('HTML color code (not color name) for the link text for all send and post services shown in the widget'),
				'since' => '3.0',
				'advanced' => true,
				'filter' => 'text',
				'default' => '',
			),
			'popup' => array(
				'required' => false,
				'name' => tra('Popup'),
				'description' => tra('Set whether the widget will show in a popup window.'),
				'since' => '3.0',
				'advanced' => true,
				'filter' => 'word',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('No'), 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'true'),
				)
			),
			'embed' => array(
				'required' => false,
				'name' => tra('Embedded Elements'),
				'description' => tra('Allow embedded elements (like flash) to be seen while iframe is loading.'),
				'since' => '3.0',
				'advanced' => true,
				'filter' => 'word',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('No'), 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'true'),
				)
			),
		)
	);
}
function wikiplugin_sharethis($data, $params)
{
	$headerlib = TikiLib::lib('header');
	extract($params, EXTR_SKIP);
	$sharethis_options = array();
	$iconcode = '';
	$sep = '&amp;';
	$comma = '%2C';
	$lb = '%23';
	$sp = '%20';

	// The following is the array that holds the default options for the plugin.
	$sharethis_options['type'] = 'website';
	$sharethis_options['sendsvcs'] = '';
	$sharethis_options['style'] = '';
	$sharethis_options['buttontext'] = '';
	$sharethis_options['postfirst'] = '';
	$sharethis_options['headertitle'] = '';
	$sharethis_options['headerbg'] = '';
	$sharethis_options['headertxtcolor'] = '';
	$sharethis_options['linkfg'] = '';
	$sharethis_options['popup'] = '';
	$sharethis_options['embed'] = '';

	// load setting options from $params

	// set post services that appear upon widget opening
	if (!empty($postfirst)) {
		$sharethis_options['postfirst'] = str_replace('|', $comma, $postfirst);
	}
	// limit send services that will appear
	if (!empty($sendsvcs)) {
		$sharethis_options['sendsvcs'] = str_replace('|', $comma, $sendsvcs);
	}
	// set icon style
	if (!empty($rotateimage) || !empty($style)) {
		if ($rotateimage == 'y' || $style == 'rotate') {
			$sharethis_options['style'] = 'rotate';
		} elseif ($style == 'horizontal') {
			$sharethis_options['style'] = 'horizontal';
		} elseif ($style == 'vertical') {
			$sharethis_options['style'] = 'vertical';
		}
	}
	if (!empty($multiple)) {
		$headerlib->add_css('body {font-family:helvetica,sans-serif;font-size:12px;}');
		$headerlib->add_css('a.stbar.chicklet img {border:0;height:16px;width:16px;margin-right:3px;vertical-align:middle;}');
		$headerlib->add_css('a.stbar.chicklet {height:16px;line-height:16px;}');
		$icons = explode('|', $multiple);
		foreach ($icons as $icon) {
			$iconcode .= '<a id="ck_' . $icon . '" class="stbar chicklet" href="javascript:void(0);">'
							. '<img src="https://ws.sharethis.com/chicklets/' . $icon . '.gif" style="margin-right:3px;" />';
			if ($icon == 'sharethis') {
				$iconcode .= 'ShareThis';
			}
			$iconcode .= '</a>';
		}
		$headerlib->add_js(
			'	var shared_object = SHARETHIS.addEntry({
						title: document.title,
						url: document.location.href
					});

					shared_object.attachButton(document.getElementById("ck_sharethis"));
					shared_object.attachChicklet("email", document.getElementById("ck_email"));
					shared_object.attachChicklet("facebook", document.getElementById("ck_facebook"));
					shared_object.attachChicklet("twitter", document.getElementById("ck_twitter"));'
		);
	}

	// set button text
	if (!empty($buttontext)) {
		$sharethis_options['buttontext'] = $buttontext;
	}
	// set header title text. If header title is set by user, then set background color and text color
	if (!empty($headertitle)) {
		$sharethis_options['headertitle'] = str_replace(' ', $sp, $headertitle);
			if (!empty($headerbg)) {
			$sharethis_options['headerbg'] = $headerbg;
			}
			if (!empty($headertxtcolor)) {
			$sharethis_options['headertxtcolor'] = $headertxtcolor;
			}
	} else {
			$sharethis_options['headerbg'] = '';
			$sharethis_options['headertxtcolor'] = '';
	}
	// set link text color for services shown in popup
	if (!empty($linkfg)) {
		$sharethis_options['linkfg'] = $linkfg;
	}
	// set popup
	if (!empty($popup)) {
		$sharethis_options['popup'] = $popup;
	}
	// set embed
	if (!empty($embed)) {
		$sharethis_options['embed'] = $embed;
	}

	// put all the options together

	$sharethiscode = "~hc~ ))ShareThis(( Bookmark Button BEGIN ~/hc~";
	$sharethiscode .= '<script type="text/javascript" src="https://ws.sharethis.com/button/sharethis.js#';
	$sharethiscode .= "type=".$sharethis_options['type'];

	if (!empty($sharethis_options['buttontext'])) $sharethiscode .= $sep."buttonText=".$sharethis_options['buttontext'];
	if (!empty($sharethis_options['popup'])) $sharethiscode .= $sep."popup=".$sharethis_options['popup'];
	if (!empty($sharethis_options['embed'])) $sharethiscode .= $sep."embeds=".$sharethis_options['embed'];
	if (!empty($sharethis_options['style'])) $sharethiscode .= $sep."style=".$sharethis_options['style'];
	if (!empty($sharethis_options['sendsvcs'])) $sharethiscode .= $sep."send_services=".$sharethis_options['sendsvcs'];
	if (!empty($sharethis_options['postfirst'])) $sharethiscode .= $sep."post_services=".$sharethis_options['postfirst'];
	if (!empty($sharethis_options['headertxtcolor'])) $sharethiscode .= $sep."headerfg=".$lb.$sharethis_options['headertxtcolor'];
	if (!empty($sharethis_options['headerbg'])) $sharethiscode .= $sep."headerbg=".$lb.$sharethis_options['headerbg'];
	if (!empty($sharethis_options['linkfg'])) $sharethiscode .= $sep."linkfg=".$lb.$sharethis_options['linkfg'];
	if (!empty($sharethis_options['headertitle'])) $sharethiscode .= $sep."headerTitle=".$sharethis_options['headertitle'];
	if (!empty($iconcode)) $sharethiscode .= ';button=false';
	$sharethiscode .= "\"></script>\n";
	if (!empty($iconcode)) $sharethiscode .= $iconcode;
	$sharethiscode .= "~hc~ ))ShareThis(( Bookmark Button END ~/hc~";

	$result = $sharethiscode;

	return $result;

}
