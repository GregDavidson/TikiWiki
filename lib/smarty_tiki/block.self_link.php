<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: block.self_link.php 59393 2016-08-09 00:39:46Z lindonb $

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * smarty_block_self_link : add a link (with A tag) to the current page on a text (passed through $content argument).
 *
 *   The generated link uses other smarty functions like query and show_sort to handle AJAX, sorting fields, and sorting icons.
 *   This block is very useful to handle table columns sorting links.
 *
 * params are the same as smarty 'query' function + some special params starting with an underscore:
 *   _sort_field : name of the field used for sorting,
 *   _sort_arg : name of the URL argument that contains the field to use for sorting. Defaults to 'sort',
 *   _ajax : if set to 'n', will force disabling AJAX even if the ajax xajax feature is enabled,	AJAX_TODO
 *   _tag : if set to 'n', will only return an URL, not the full A tag + text (AJAX and sorting features are not available in this case),
 *   _class : CSS class to use for the A tag
 *   _template : (see smarty query function 'template' param)
 *   _htmlelement : (see smarty query function 'htmlelement' param)
 *   _icon : file name of the icon to use (e.g. 'page_edit', 'cross', ...) - only works with legacy icons
 *   _icon_name : name of the icon to use in order to use iconsets
 *   _icon_class : CSS class to use for the icon's IMG tag
 *   _icon_size : size of icon when _icon_name is used
 *   _menu_text : (see smarty icon function)
 *   _menu_icon : (see smarty icon function)
 *   _title : tooltip to display when the mouse is over the link. Use $content when _icon is used.
 *   _text : show text as part of the link (for instance, after the icon for a menu item)
 *   _alt : alt attribute for the icon's IMG tag (use _title if _alt is not specified).
 *   _script : specify another script than the current one (this disable AJAX for this link when the current script is different).
 *   _on* : specify values of on* (e.g. onclick) HTML attributes used for javascript events
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_self_link($params, $content, $smarty, &$repeat = false)
{
	$default_type = 'absolute_path';
	$default_icon_type = 'relative';

	if ( $repeat ) return;
	$smarty->loadPlugin('smarty_function_query');

	if ( is_array($params) ) {
		if ( ! isset($content) ) $content = '';
		if ( ! isset($params['_ajax']) ) $params['_ajax'] = 'y';
		if ( ! isset($params['_script']) ) $params['_script'] = '';
		if ( ! isset($params['_tag']) ) $params['_tag'] = 'y';
		if ( ! empty($params['_anchor']) ) $anchor = $params['_anchor']; else $anchor = '';
		if ( empty($params['_disabled']) ) {
			if ( ! isset($params['_sort_arg']) ) $params['_sort_arg'] = 'sort';
			if ( ! isset($params['_sort_field']) ) {
				$params['_sort_field'] = '';
			} elseif ( $params['_sort_arg'] != '' and ! isset($params[$params['_sort_arg']]) ) {
				$params[$params['_sort_arg']] = $params['_sort_field'].'_asc,'.$params['_sort_field'].'_desc';
			}
			// Complete _script path if needed (not empty, not an anchor, ...)
			if ( !empty($params['_script']) && $params['_script'][0] != '#' && $params['_script'] != 'javascript:void(0)' && stripos($params['_script'], 'mailto:') !== 0) {
				if ( $_SERVER['PHP_SELF'][0] == '/' && strpos($params['_script'], '/') === false ) {
					$self_dir = str_replace('\\', '/', dirname($_SERVER['PHP_SELF']));
					$params['_script'] = ( $self_dir == '/' ? '' : $self_dir ).'/'.$params['_script'];
				}
				if ( $params['_script'] == $_SERVER['PHP_SELF'] ) {
					$params['_script'] = '';
				}
			}

			$params['_type'] = $default_type;
			$ret = smarty_function_query($params, $smarty);
		}

		if ( $params['_tag'] == 'y' ) {

			if ( empty($params['_disabled']) ) {
				if ( $params['_ajax'] === 'y' && $params['_script'] === '' ) {
					$smarty->loadPlugin('smarty_block_ajax_href');
					if ( ! isset($params['_htmlelement']) ) $params['_htmlelement'] = 'role_main';
					if ( ! isset($params['_onclick']) ) $params['_onclick'] = '';
					if ( ! isset($params['_template']) ) {
						$params['_template'] = basename($_SERVER['PHP_SELF'], '.php').'.tpl';
						if ( $params['_template'] == 'tiki-index.tpl' ) $params['_template'] = 'tiki-show_page.tpl';
					}
					if ( ! file_exists('templates/'.$params['_template']) || $params['_template'] == 'noauto' ) {
						$params['_htmlelement'] = '';
						$params['_template'] = '';
					}
					$ret = smarty_block_ajax_href(
						array(
							'template' => $params['_template'],
							'htmlelement' => $params['_htmlelement'],
							'_onclick' => $params['_onclick'],
							'_anchor'=> $anchor
						),
						$ret,
						$smarty,
						$tmp = false
					);
				} else {
					$ret = 'href="' . $ret . '"';
				}
			}

			if ( isset($params['_icon']) || isset($params['_icon_name'])) {
				if (! isset($params['_title']) && $content != '' && (!isset($params['_rel']) ||
						strpos($params['_rel'], 'box') === false)) $params['_title'] = $content;
				$smarty->loadPlugin('smarty_function_icon');
				if (isset($params['_icon'])) {
					$icon_params['_id'] = $params['_icon'];
				} else {
					$icon_params['name'] = $params['_icon_name'];
				}
				$icon_params['_type'] = $default_icon_type;
				if ( isset($params['_alt']) ) {
					$icon_params['alt'] = $params['_alt'];
				} elseif ( isset($params['_title']) ) {
					$icon_params['alt'] = $params['_title'];
					$icon_params['title'] = ''; // will already be included in the surrounding A tag
				}

				if ( isset($params['_menu_text']) && $params['_menu_text'] == 'y' ) {
					$icon_params['_menu_text'] = $params['_menu_text'];
					$icon_params['title'] = $params['_title']; // Used as the menu text
					$params['_title'] = ''; // will already be displayed as the menu text
				}
				if ( isset($params['_menu_icon']) ) $icon_params['_menu_icon'] = $params['_menu_icon'];
				if ( isset($params['_icon_class']) ) $icon_params['class'] = $params['_icon_class'];
				if ( isset($params['_icon_size']) ) $icon_params['size'] = $params['_icon_size'];

				if ( isset($params['_width']) ) $icon_params['width'] = $params['_width'];
				if ( isset($params['_height']) ) $icon_params['height'] = $params['_height'];

				$content = smarty_function_icon($icon_params, $smarty);

				if (isset($params['_text'])) {
					$content .= ' ' . $params['_text'];
				}
			}

			$link = ( !empty($params['_class']) ? 'class="'.$params['_class'].'" ' : '' )
				. ( !empty($params['_style']) ? 'style="' . $params['_style'] . '" ' : '' )
				. ( !empty($params['_title']) ? 'title="' . str_replace('"', '\"', $params['_title']) . '" ' : '' );
			if (!empty($params['_rel'])) {
				if (strpos($params['_rel'], 'box') !== false) {
					$rel = 'data-box="box" ';
				} else {
					$rel = 'rel="' . str_replace('"', '\"', $params['_rel']) . '" ';
				}
			} else {
				$rel = '';
			}
			$link .= $rel;
			foreach ( $params as $k => $v ) {
				if ( strlen($k) > 3 && substr($k, 0, 3) == '_on' && !empty($v) ) {
					$link .= htmlentities(substr($k, 1)).'="'.$v.'" '; // $v should be already htmlentitized in the template
					unset($params[$k]);
				}
			}
			$link .= $ret;

			if (isset($params['_confirm'])) {
				$link .= ' data-confirm="' . smarty_modifier_escape($params['_confirm']) . '"';
			}

			$ret = "<a $link>".$content.'</a>';

			if ( !empty($params['_sort_field']) ) {
				$smarty->loadPlugin('smarty_function_show_sort');
				$ret = "<a $link style='text-decoration:none;'>" . $content .
								smarty_function_show_sort(
									array('sort' => $params['_sort_arg'], 'var' => $params['_sort_field']),
									$smarty
								) . '</a>';
			}
		}
	} else {
		$params = array('_type' => $default_type);
		$ret = smarty_function_query($params, $smarty);
	}

	return $ret;
}
