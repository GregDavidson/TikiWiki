<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: modifier.pagename.php 59398 2016-08-09 04:22:35Z fvtorres $

// Translate only if feature_multilingual is on

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_modifier_pagename($source)
{
	global $prefs;
	if (!empty($prefs['wiki_pagename_strip']) || $prefs['namespace_indicator_in_page_title'] == 'y') {
    	if (!empty($prefs['wiki_pagename_strip'])) {
	    	$wiki_strip = '~'. preg_quote($prefs['wiki_pagename_strip']) . '.*$~';
	    	$source = preg_replace($wiki_strip, '', $source);
	    }
	    if ($prefs['namespace_indicator_in_page_title'] == 'y') {
	    	$wiki_namespace = '~.* / ~';
	    	$source = preg_replace($wiki_namespace, '', $source);
	    }
	    return $source;
	} else {
		return $source;
	}
}
