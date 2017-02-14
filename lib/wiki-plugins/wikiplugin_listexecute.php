<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_listexecute.php 60502 2016-12-01 11:50:43Z kroky6 $

function wikiplugin_listexecute_info()
{
	return array(
		'name' => tra('List Execute'),
		'documentation' => 'PluginListExecute',
		'description' => tra('Set custom actions that can be executed on a filtered list of objects'),
		'prefs' => array('wikiplugin_listexecute', 'feature_search'),
		'body' => tra('List configuration information'),
		'validate' => 'all',
		'filter' => 'wikicontent',
		'profile_reference' => 'search_plugin_content',
		'iconname' => 'list',
		'introduced' => 11,
		'tags' => array( 'advanced' ),
		'params' => array(
		),
	);
}

function wikiplugin_listexecute($data, $params)
{
	static $iListExecute = 0;
	$iListExecute++;

	$unifiedsearchlib = TikiLib::lib('unifiedsearch');

	$actions = array();

	$factory = new Search_Action_Factory;
	$factory->register(
		array(
			'change_status' => 'Search_Action_ChangeStatusAction',
			'delete' => 'Search_Action_Delete',
			'email' => 'Search_Action_EmailAction',
			'wiki_approval' => 'Search_Action_WikiApprovalAction',
			'tracker_item_modify' => 'Search_Action_TrackerItemModify',
			'filegal_change_filename' => 'Search_Action_FileGalleryChangeFilename',
			'filegal_image_overlay' => 'Search_Action_FileGalleryImageOverlay',
		)
	);

	$query = new Search_Query;
	$unifiedsearchlib->initQuery($query);

	$matches = WikiParser_PluginMatcher::match($data);

	$builder = new Search_Query_WikiBuilder($query);
	$builder->apply($matches, true);
	$tsret = $builder->applyTablesorter($matches, true);
	if (!empty($tsret['max']) || !empty($_GET['numrows'])) {
		$max = !empty($_GET['numrows']) ? $_GET['numrows'] : $tsret['max'];
		$builder->wpquery_pagination_max($query, $max);
	}
	$paginationArguments = $builder->getPaginationArguments();

	if (!empty($_REQUEST[$paginationArguments['sort_arg']])) {
		$query->setOrder($_REQUEST[$paginationArguments['sort_arg']]);
	}

	$customOutput = false;

	foreach ($matches as $match) {
		$name = $match->getName();

		if ($name == 'action') {
			$action = $factory->fromMatch($match);

			if ($action && $action->isAllowed(Perms::get()->getGroups())) {
				$actions[$action->getName()] = $action;
			}
		}

		if ($name == 'output')
			$customOutput = true;
	}

	$index = $unifiedsearchlib->getIndex();

	$result = $query->search($index);
	$result->setId('wplistexecute-' . $iListExecute);

	$dataSource = $unifiedsearchlib->getDataSource();
	$builder = new Search_Formatter_Builder;
	$builder->setPaginationArguments($paginationArguments);
	$builder->setActions($actions);
	$builder->setId('wplistexecute-' . $iListExecute);
	$builder->setCount($result->count());
	$builder->setTsOn($tsret['tsOn']);
	$builder->apply($matches);

	$result->setTsSettings($builder->getTsSettings());
	$result->setTsOn($tsret['tsOn']);

	$formatter = $builder->getFormatter();

	if( !$customOutput ) {
		$plugin = new Search_Formatter_Plugin_SmartyTemplate('templates/wiki-plugins/wikiplugin_listexecute.tpl');
		$plugin->setFields(array('report_status' => null));
		$plugin->setData(
			array(
				'actions' => $actions,
				'iListExecute' => $iListExecute
			)
		);
		$formatter = new Search_Formatter($plugin);
	}

	if (isset($_POST['list_action'], $_POST['objects'])) {
		$action = $_POST['list_action'];
		$objects = (array) $_POST['objects'];

		if (isset($actions[$action])) {
			$reportSource = new Search_Action_ReportingTransform;

			$tx = TikiDb::get()->begin();

			$action = $actions[$action];
			$list = $formatter->getPopulatedList($result);

			foreach ($list as $entry) {
				$identifier = "{$entry['object_type']}:{$entry['object_id']}";
				if (in_array($identifier, $objects) || in_array('ALL', $objects)) {
					if( isset($_POST['list_input']) ) {
						$entry['value'] = $_POST['list_input'];
					}
					
					$success = $action->execute($entry);
					if( !$success ) {
						Feedback::error(tr("Error executing action %0 on item %1.", $_POST['list_action'], $entry['title']));
					}

					$reportSource->setStatus($entry['object_type'], $entry['object_id'], $success);
				}
			}

			$tx->commit();

			$result->applyTransform($reportSource);
		}
	}

	return $formatter->format($result);
}
