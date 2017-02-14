<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_quote.php 59968 2016-10-12 22:25:15Z rjsmelo $

function wikiplugin_quote_info()
{
	return array(
		'name' => tra('Quote'),
		'documentation' => 'PluginQuote',
		'description' => tra('Format text as a quote'),
		'prefs' => array( 'wikiplugin_quote' ),
		'body' => tra('Quoted text'),
		'iconname' => 'quotes',
		'introduced' => 1,
		'filter' => 'text',
		'tags' => array( 'basic' ),
		'params' => array(
			'replyto' => array(
				'required' => false,
				'name' => tra('Reply To'),
				'description' => tra('Name of the quoted person.'),
				'since' => '1',
				'filter' => 'text',
				'default' => '',
			),
			'thread_id' => array(
				'required' => false,
				'name' => tra('Thread Id for Forum replies'),
				'description' => tra('The thread Id of the comment being replied to in forums. Overwrites replyto'),
				'since' => '15',
				'filter' => '	text',
				'default' => '',
			),
			'source_url' => array(
				'required' => false,
				'name' => tra('Source URL'),
				'description' => tra('The URL to the source quoted.'),
				'since' => '16',
				'filter' => 'text',
				'default' => '',
			),
		),
	);
}

function wikiplugin_quote($data, $params)
{
	global $smarty;

	$source_url = '';

	if ($params['thread_id']) {
		$commentslib = TikiLib::lib('comments');
		$comment_info = $commentslib->get_comment($params['thread_id']);
		$replyto = $comment_info['userName'];
	} elseif ($params['replyto']) {
		$replyto = $params['replyto'];
	}
	if ($params['source_url']) {
		$source_url = $params['source_url'];
	}

	$smarty->assign('comment_info', $comment_info);
	$smarty->assign('replyto', $replyto);
	$smarty->assign('data', trim($data));
	$smarty->assign('source_url', trim($source_url));

	return $smarty->fetch("wiki-plugins/wikiplugin_quote.tpl");
}
