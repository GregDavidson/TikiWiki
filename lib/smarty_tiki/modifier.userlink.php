<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: modifier.userlink.php 59753 2016-09-20 14:13:16Z patrick-proulx $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * \brief Smarty modifier plugin to create user links with optional mouseover info
 *
 * - type:     modifier
 * - name:     userlink
 * - purpose:  to return a user link
 *
 * @author
 * @param string class (optional)
 * @param string idletime (optional)
 * @param string fullname (optional)
 * @param integer max_length (optional)
 * @return string user link
 *
 * Syntax: {$foo|userlink[:"<class>"][:"<idletime>"][:"<fullname>"][:<max_length>]} (optional params in brackets)
 *
 * Example: {$userinfo.login|userlink:'link':::25}
 */

function smarty_modifier_userlink($other_user, $class='userlink', $idletime='not_set', $fullname='', $max_length=0, $popup='')
{
	global $prefs;

	if (empty($other_user)){
		return "";
	}
	if (is_array($other_user)) {
		if (count($other_user) > 1) {
			$other_user = array_map(
				function ($username) use ($class, $idletime, $popup) {
					return smarty_modifier_userlink($username, $class, $idletime, '', 0, $popup);
				},
				$other_user
			);

			$last = array_pop($other_user);
			return tr('%0 and %1', implode(', ', $other_user), $last);
		} else {
			$other_user = reset($other_user);
		}
	}
	if (!$fullname) {
		$fullname = TikiLib::lib('user')->clean_user($other_user);
	}
	if ($max_length) {
		TikiLib::lib('smarty')->loadPlugin('smarty_modifier_truncate');
		$fullname = smarty_modifier_truncate($fullname, $max_length, '...', true);
	}

	if (empty($popup) && $prefs['feature_community_mouseover'] == 'n') {
		$popup = 'n';
	} else {
		$popup = 'y';
	}

	return TikiLib::lib('user')->build_userinfo_tag($other_user, $fullname, $class, $popup);
}
