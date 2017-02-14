<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_iframe.php 59376 2016-08-05 13:56:48Z xavidp $

function wikiplugin_iframe_info()
{
	return array(
		'name' => tra('Iframe'),
		'documentation' => 'PluginIframe',
		'description' => tra('Include the body of another web page in a scrollable frame within a page'),
		'prefs' => array( 'wikiplugin_iframe' ),
		'body' => tra('URL'),
		'format' => 'html',
		'validate' => 'all',
		'tags' => array( 'basic' ),
		'iconname' => 'copy',
		'introduced' => 3,
		'params' => array(
			'name' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Name'),
				'description' => tra('Name'),
				'since' => '3.0',
				'filter' => 'text',
				'default' => '',
			),
			'title' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Title'),
				'description' => tra('Frame title'),
				'since' => '3.2',
				'filter' => 'text',
				'default' => '',
			),
			'width' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Width in pixels or %'),
				'since' => '3.0',
				'filter' => 'text',
				'default' => '',
			),
			'height' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Pixels or %'),
				'since' => '3.0',
				'filter' => 'text',
				'default' => '',
			),
			'align' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Alignment'),
				'description' => tra('Align the iframe on the page'),
				'since' => '3.0',
				'filter' => 'word',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Top'), 'value' => 'top'), 
					array('text' => tra('Middle'), 'value' => 'middle'), 
					array('text' => tra('Bottom'), 'value' => 'bottom'), 
					array('text' => tra('Left'), 'value' => 'left'), 
					array('text' => tra('Right'), 'value' => 'right') 
				)
			),
			'frameborder' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Frame Border'),
				'description' => tra('Choose whether to show a border around the iframe'),
				'since' => '3.0',
				'filter' => 'digits',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0)
				)
			),
			'marginheight' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Margin Height'),
				'description' => tra('Margin height in pixels'),
				'since' => '3.0',
				'filter' => 'digits',
				'default' => '',
			),
			'marginwidth' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Margin Width'),
				'description' => tra('Margin width in pixels'),
				'since' => '3.0',
				'filter' => 'digits',
				'default' => '',
			),
			'scrolling' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Scrolling'),
				'description' => tra('Choose whether to add a scroll bar'),
				'since' => '3.0',
				'filter' => 'word',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'yes'), 
					array('text' => tra('No'), 'value' => 'no'),
					array('text' => tra('Auto'), 'value' => 'auto'),
				)
			),
			'src' => array(
				'required' => false,
				'name' => tra('URL'),
				'description' => tra('URL'),
				'filter' => 'url',
				'since' => '3.0',
				'default' => '',
			),
			'responsive' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Responsive'),
				'description' => tra('Make the display responsive so that browsers determine dimensions based on the width of their containing block by creating an intrinsic ratio that will properly scale on any device.'),
				'since' => '16.0',
				'filter' => 'word',
				'default' => '16by9',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('16 by 9'), 'value' => '16by9'),
					array('text' => tra('4 by 3'), 'value' => '4by4'),
					array('text' => tra('no'), 'value' => 'no'),
				)
			),
		), 
	);
}

function wikiplugin_iframe($data, $params)
{

	extract($params, EXTR_SKIP);
	if (isset($responsive) AND $responsive != 'no' AND $responsive != 'n') {
		if ($responsive == '4by3' ) {
			$ret = '<div class="embed-responsive embed-responsive-4by3"><iframe class="embed-responsive-item" ';
		} else {
			$ret = '<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" ';
		}
	} else {
		$ret = '<iframe ';
	}

	if (isset($name)) {
		$ret .= " name=\"$name\"";
	}
	if (isset($title)) {
		$ret .= " title=\"$title\"";
	}
	if (isset($width)) {
		$ret .= " width=\"$width\"";
	}
	if (isset($height)) {
		$ret .= " height=\"$height\"";
	}
	if (isset($align)) {
		$ret .= " align=\"$align\"";
	}
	if (isset($frameborder)) {
		$ret .= " frameborder=\"$frameborder\"";
	}
	if (isset($marginheight)) {
		$ret .= " marginheight=\"$marginheight\"";
	}
	if (isset($marginwidth)) {
		$ret .= " marginwidth=\"$marginwidth\"";
	}
	if (isset($scrolling)) {
		$ret .= " scrolling=\"$scrolling\"";
	}
	if (isset($src)) {
		$ret .= " src=\"$src\"";
	} elseif (!empty($data)) {
		$ret .= " src=\"$data\"";
	}
	if (strpos($src, 'ViewerJS') !== false) {
		$ret .= " allowfullscreen webkitallowfullscreen";
	}
	if (isset($responsive) AND $responsive != 'no' AND $responsive != 'n') {
		$ret .= ">$data</iframe></div>";
	} else {
		$ret .= ">$data</iframe>";
	}
	return $ret;
}
