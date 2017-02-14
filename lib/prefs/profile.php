<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: profile.php 57966 2016-03-17 20:05:33Z jonnybradley $

function prefs_profile_list()
{
	return array(
		'profile_sources' => array(
			'name' => tra('Repository URLs'),
			'description' => tra('List of URLs for the profile repositories that will be used.'),
			'type' => 'textarea',
			'size' => 5,
			'hint' => tra('Enter multiple repository URLs, one per line.'),
			'default' => 'http://profiles.tiki.org/profiles',
		),
		'profile_channels' => array(
			'name' => tra('Data channels'),
			'description' => tra('Data channels are templates that can be applied from a post request. They can be used to automate work on more complex installations.'),
			'type' => 'textarea',
			'size' => 5,
			'hint' => tra('Data channels create a named pipe to run profiles from user space. One channel per line. Each line is comma delimited and contains __channel name, domain, profile, allowed groups, (optional) $profilerequest:input$ matches to groups__.'),
			'help' => 'http://profiles.tiki.org/Data+Channels',
			'warning' => tra('There are security considerations related to using data channels. Make sure the profile page is controlled by administrators only.'),
			'default' => '',
		),
		'profile_unapproved' => array(
			'name' => tra('Developer mode'),
			'description' => tra('For profiles under an approval workflow, always use the latest version, even if not approved.'),
			'type' => 'flag',
			'warning' => tra('Make sure you review the profiles you install.'),
			'default' => 'n',
		),
	);
}

