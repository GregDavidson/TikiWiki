<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_attach.php 57962 2016-03-17 20:02:39Z jonnybradley $

function wikiplugin_attach_info()
{
	return array(
		'name' => tra('Attachment'),
		'documentation' => 'PluginAttach',
		'description' => tra('Display an attachment or a list of them'),
		'prefs' => array( 'feature_wiki_attachments', 'wikiplugin_attach' ),
		'body' => tra("Comment"),
		'iconname' => 'attach',
		'introduced' => 1,
		'params' => array(
			'name' => array(
				'required' => false,
				'name' => tra('Name'),
				'description' => tra('File name of the attached file to link to. Either name, file, id or num can be
					used to identify a single attachment'),
				'since' => '1',
				'filter' => 'text',
				'default' => '',
			),
			'file' =>array(
				'required' => false,
				'name' => tra('File'),
				'description' => tra('Same as name'),
				'since' => '1',
				'filter' => 'text',
				'default' => '',
			),
			'page' => array(
				'required' => false,
				'name' => tra('Page'),
				'description' => tra('Name of the wiki page the file is attached to. If left empty when the plugin is
					used on a wiki page, this defaults to that wiki page.'),
				'since' => '1',
				'filter' => 'pagename',
				'default' => '',
				'profile_reference' => 'wiki_page',
			),
			'showdesc' => array(
				'required' => false,
				'name' => tra('Show Description'),
				'description' => tra('Shows the description as the link text instead of the file name (not used by default)'),
				'since' => '1',
				'filter' => 'digits',
				'default' => 0,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0),
				),
			),
			'bullets' => array(
				'required' => false,
				'name' => tra('Bullets'),
				'description' => tra('Makes the list of attachments a bulleted list (not set by default)'),
				'since' => '1',
				'filter' => 'digits',
				'default' => 0,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0),
				),
			),
			'image' =>array(
				'required' => false,
				'name' => tra('Image'),
				'description' => tr('Indicates that this file is an image, and should be displayed inline using the
					%0 tag (not set by default)', '<code>img</code>'),
				'since' => '1',
				'filter' => 'digits',
				'default' => 0,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0),
				),
			),
			'inline' =>array(
				'required' => false,
				'name' => tra('Custom Label'),
				'description' => tr('Makes the text between the %0 tags the link text instead of the file name
					or description. Only the first attachment will be listed.', '<code>{ATTACH}</code>'),
				'since' => '1',
				'filter' => 'digits',
				'default' => 0,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0),
				),
			),
			'all' => array(
				'required' => false,
				'name' => tra('All'),
				'description' => tr('Lists links to all attachments for the entire tiki site together with pages they
					are attached to when set to %0 (Yes)', '<code>1</code>'),
				'since' => '1',
				'filter' => 'digits',
				'default' => 0,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0),
				),
			),
			'num' => array(
				'required' => false,
				'name' => tra('Order Number'),
				'description' => tra('Identifies the attachment to link to by the order of the attachment in the list
					of attachments to a page instead of by file name or ID. Either name, file, id or num can be used to
					identify a single attachment.'),
				'since' => '1',
				'default' => '',
			),
			'id' => array(
				'required' => false,
				'name' => tra('ID'),
				'description' => tra('Identifies the attachment to link to by id number instead of by file name or order
					number. Either name, file, id or num can be used to identify a single attachment.'),
				'since' => '1',
				'accepted' => tra('Attachment name, file, id or num'),
				'filter' => 'text',
			),
			'dls' => array(
				'required' => false,
				'name' => tra('Downloads'),
				'description' => tr('The alt text that pops up on mouseover will include the number of downloads of the
					attachment at the end when set to %0 (Yes)', '<code>1</code>'),
				'since' => '1',
				'filter' => 'digits',
				'default' => 0,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0),
				),
			),
			'icon' =>array(
				'required' => false,
				'name' => tra('File Type Icon'),
				'description' => tr('A file type icon is displayed in front of the attachment link when this is set to
					%0 (Yes)', '<code>1</code>'),
				'since' => '1',
				'filter' => 'digits',
				'default' => 0,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0),
				),
			),

		),
	);
}

function wikiplugin_attach($data, $params)
{
	global $atts;
	global $mimeextensions;
	global $user, $section, $section_class;

	$wikilib = TikiLib::lib('wiki');
	$tikilib = TikiLib::lib('tiki');

	extract($params, EXTR_SKIP);

	$loop = array();
	if (!isset($atts))
		$atts = array();

	if ( ! is_array($atts) || ! array_key_exists("data", $atts) || count($atts["data"]) < 1 ) {
		// We're being called from a preview or something; try to build the atts ourselves.

		// See if we're being called from a tracker page.
		if ( $section == 'trackers' ) {
			$trklib = TikiLib::lib('trk');
			$atts_item_name = $_REQUEST["itemId"];
			$tracker_info = $trklib->get_tracker($atts_item_name);
			$tracker_info = array_merge($tracker_info, $trklib->get_tracker_options($atts_item_name));

			$attextra = 'n';

			if (strstr($tracker_info["orderAttachments"], '|')) {
				$attextra = 'y';
			}

			$attfields = explode(',', strtok($tracker_info["orderAttachments"], '|'));

			$atts = $trklib->list_item_attachments($atts_item_name, 0, -1, 'comment_asc', '');
		}

		// See if we're being called from a wiki page.
		if (strstr($section_class, 'wiki_page')) {
			$atts_item_name = $_REQUEST["page"];
			$atts = $wikilib->list_wiki_attachments($atts_item_name, 0, -1, 'created_desc', '');
		}
	}

	// Save for restoration before this script ends
	$old_atts = $atts;
	$url = '';

	if (isset($all) && intval($all)>0) {
		$atts = $wikilib->list_all_attachements(0, -1, 'page_asc', '');
	} elseif (!empty($page)) {
		if (!$tikilib->page_exists($page)) {
			return "''".tr('Page "%0" does not exist', $page)."''";
		}
		if ($tikilib->user_has_perm_on_object($user, $page, 'wiki page', 'tiki_p_wiki_view_attachments') || $tikilib->user_has_perm_on_object($user, $_REQUEST['page'], 'wiki page', 'tiki_p_wiki_admin_attachments')) {
			$atts = $wikilib->list_wiki_attachments($page, 0, -1, 'created_desc', '');
			$url = "&amp;page=$page";
		}
	}

	if ( ! array_key_exists("cant", $atts) ) {
		if ( array_key_exists("data", $atts) ) {
			$atts['cant'] = count($atts["data"]);
		} else {
			$atts['cant'] = 0;
			$atts["data"] = "";
		}
	}

	if (!isset($num)) $num = 0;
	if (!isset($id)) {
		$id = 0;
	} else {
		$num = 0;
	}

	if ( isset( $file ) ) {
		$name = $file;
	}

	if ( isset( $name ) ) {
		$id = 0;
		$num = 0;
	} else {
		$name = '';
	}

	if (!$atts['cant']) {
		return "''".tra('No such attachment on this page')."''";
	} elseif ($num > 0 and $num < ($atts['cant']+1)) {
		$loop[] = $num;
	} else {
		$loop = range(1, $atts['cant']);
	}

	$out = array();
	if ($data) {
		$out[] = $data;
	}

	foreach ($loop as $n) {
		$n--;
		if ( (!$name and !$id) or $id == $atts['data'][$n]['attId'] or $name == $atts['data'][$n]['filename'] ) {
			$link = "";
			if ( isset( $bullets ) && $bullets ) {
				$link .= "<li>";
			}

		if (isset($image) and $image ) {
			$link.= '<img src="tiki-download_wiki_attachment.php?attId='.$atts['data'][$n]['attId'].$url.'" class="wiki"';
			$link.= ' alt="';
			if (empty($showdesc) || empty($atts['data'][$n]['comment'])) {
				$link.= $atts['data'][$n]['filename'];
			} else {
				$link.= $atts['data'][$n]['comment'];
			}
			if (isset($dls)) {
				$link.= " ".$atts['data'][$n]['hits'];
			}
			$link.= '"/>';
		} else {
			$link.= '<a href="tiki-download_wiki_attachment.php?attId='.$atts['data'][$n]['attId'].$url.'&amp;download=y" class="wiki"';
			$link.= ' title="';

			if (empty($showdesc) || empty($atts['data'][$n]['comment'])) {
				$link.= $atts['data'][$n]['filename'];
			} else {
				$link.= $atts['data'][$n]['comment'];
			}
			if (isset($dls)) {
				$link.= " ".$atts['data'][$n]['hits'];
			}

			$link.= '">';
			if (isset($icon)) {
				$smarty = TikiLib::lib('smarty');
				$smarty->loadPlugin('smarty_modifier_iconify');
				$iconhtml = smarty_modifier_iconify($atts['data'][$n]['filename']);
				$link .= $iconhtml . '&nbsp';
			}

			if (!empty($showdesc) && !empty($atts['data'][$n]['comment'])) {
				$link.= strip_tags($atts['data'][$n]['comment']);
			} else if ( !empty( $inline ) && !empty($data)) {
				$link.= $data;
			} else {
				$link.= strip_tags($atts['data'][$n]['filename']);
			}

			$link.= '</a>';

			$pageall = strip_tags($atts['data'][$n]['page']);
			if ( isset( $all ) ) {
				$link.= " attached to ".'<a title="'.$pageall.'" href="'.$pageall.'" class="wiki">'.$pageall.'</a>';
			}

		}

		if ( isset( $bullets ) && $bullets ) {
			$link .= "</li>";
		}

		$out[] = $link;
		}
	}

	if ( isset( $bullets ) && $bullets ) {
		$separator = "\n";
	} else {
		$separator = "<br />\n";
	}

	if ( !empty( $inline ) && !empty($data) ) {
		if ( array_key_exists(1, $out) ) {
			$data = $out[1];
		} else {
			$data = "";
		}
	} else {
		$data = implode($separator, $out);
	}

	if ( isset( $bullets ) && $bullets ) {
		$data = "<ul>".$data."</ul>";
	}

	if ( strlen($data) == 0 ) {
		$data = "<strong>".tra('No such attachment on this page')."</strong>";
	}

	$atts = $old_atts;

	return '~np~'.$data.'~/np~';
}
