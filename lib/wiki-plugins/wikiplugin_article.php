<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_article.php 57962 2016-03-17 20:02:39Z jonnybradley $

function wikiplugin_article_info()
{
	return array(
		'name' => tra('Article'),
		'documentation' => 'PluginArticle',
		'description' => tra('Display a field of an article'),
		'prefs' => array( 'feature_articles', 'wikiplugin_article' ),
		'iconname' => 'articles',
		'format' => 'html',
		'introduced' => 1,
		'params' => array(
			'Field' => array(
				'required' => false,
				'name' => tra('Field'),
				'description' => tra('The article field (component) to display. The default field is "Heading".'),
				'filter' => 'word',
				'default' => 'heading',
				'since' => '1',
			),
			'Id' => array(
				'required' => false,
				'name' => tra('Article ID'),
				'description' => tra('The article to display. If no value is provided, the most recent article will be used.'),
				'filter' => 'digits',
				'default' => '',
				'profile_reference' => 'article',
				'since' => '1',
			),
		),
	);
}

function wikiplugin_article($data, $params)
{
	global $user, $tiki_p_admin_cms;
	$userlib = TikiLib::lib('user');
	$tikilib = TikiLib::lib('tiki');
	$statslib = TikiLib::lib('stats');
	$artlib = TikiLib::lib('art');
	$smarty = TikiLib::lib('smarty');

	extract($params, EXTR_SKIP);

	if (empty($Id)) {

		$Id = $artlib->get_most_recent_article_id();
	}
	if (!isset($Field)) {
		$Field = 'heading';
	} 

	if ($tiki_p_admin_cms == 'y' || $tikilib->user_has_perm_on_object($user, $Id, 'article', 'tiki_p_edit_article')
		|| (isset($article_data) && $article_data["author"] == $user && $article_data["creator_edit"] == 'y'))
	{
		$smarty->loadPlugin('smarty_function_icon');
		$add="&nbsp;<a href='tiki-edit_article.php?articleId=$Id' class='editplugin'>" .
			smarty_function_icon(['name' => 'edit'], $smarty)  . '</a>';
	} else {
		$add="";
	}

	$article_data = $artlib->get_article($Id);
	if (isset($article_data[$Field])) {
		return $tikilib->parse_data($article_data[$Field]) . $add;
	}
}
