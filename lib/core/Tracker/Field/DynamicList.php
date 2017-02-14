<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: DynamicList.php 60602 2016-12-12 11:39:29Z kroky6 $

/**
 * Handler class for DynamicList
 *
 * Letter key: ~w~
 *
 */
// TODO: validate parameters (several required)
class Tracker_Field_DynamicList extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		return array(
			'w' => array(
				'name' => tra('Dynamic Items List'),
				'description' => tra('Dynamically updates a selection list based on linked data from another tracker.'),
				'help' => 'Dynamic items list',
				'prefs' => array('trackerfield_dynamiclist'),
				'tags' => array('advanced'),
				'default' => 'n',
				'params' => array(
					'trackerId' => array(
						'name' => tr('Tracker ID'),
						'description' => tr('Tracker to link with'),
						'filter' => 'int',
						'legacy_index' => 0,
						'profile_reference' => 'tracker',
					),
					'filterFieldIdThere' => array(
						'name' => tr('Field ID (Other tracker)'),
						'description' => tr('Field ID to link with in the other tracker'),
						'filter' => 'int',
						'legacy_index' => 1,
						'profile_reference' => 'tracker_field',
						'parent' => 'trackerId',
						'parentkey' => 'tracker_id',
					),
					'filterFieldIdHere' => array(
						'name' => tr('Field ID (This tracker)'),
						'description' => tr('Field ID to link with in the current tracker'),
						'filter' => 'int',
						'legacy_index' => 2,
						'profile_reference' => 'tracker_field',
						'parent' => 'input[name=trackerId]',
						'parentkey' => 'tracker_id',
					),
					'listFieldIdThere' => array(
						'name' => tr('Listed Field'),
						'description' => tr('Field ID to be displayed in the dropdown list.'),
						'filter' => 'int',
						'legacy_index' => 3,
						'profile_reference' => 'tracker_field',
						'parent' => 'trackerId',
						'parentkey' => 'tracker_id',
					),
					'statusThere' => array(
						'name' => tr('Status Filter'),
						'description' => tr('Restrict listed items to specific statuses.'),
						'filter' => 'alpha',
						'options' => array(
							'opc' => tr('all'),
							'o' => tr('open'),
							'p' => tr('pending'),
							'c' => tr('closed'),
							'op' => tr('open, pending'),
							'pc' => tr('pending, closed'),
						),
						'legacy_index' => 4,
					),
					'hideBlank' => array(
						'name' => tr('Hide blank'),
						'description' => tr('Hide first blank option, thus preselecting the first available option.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
						'legacy_index' => 5,
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$ins_id = $this->getInsertId();
		return array(
			'value' => (isset($requestData[$ins_id]))
				? $requestData[$ins_id]
				: $this->getValue(),
		);
	}

	function renderInput($context = array())
	{
		// REFACTOR: can't use list-tracker_field_values_ajax.php yet as it doesn't seem to filter
		
		
		// Modified to support multiple dynamic item list fields bound to the same $filterFieldIdHere
		// When changing  $filterFieldValueHere (i.e combobox) the $originalValue will be send as part of the request to the backend.
		// The backend returns an json array('request' => $requestData, 'response' => $responseData).
		// This way we can keep the default $originalValue, even when changing the selection forth and back.
		// It fixes also the issue that, if more than one dynamic item list fields are set and use the same
		// $filterFieldIdHere, then the initial value was wrong due to multiple fires of the handler.

		$filterFieldIdHere = trim($this->getOption('filterFieldIdHere'));
		$trackerIdThere = $this->getOption('trackerId');
		$listFieldIdThere = $this->getOption('listFieldIdThere');
		$filterFieldIdThere = $this->getOption('filterFieldIdThere');
		$statusThere = $this->getOption('statusThere');
		$isMandatory = $this->getConfiguration('isMandatory');
		$insertId = $this->getInsertId();
		$originalValue = $this->getConfiguration('value');
		$hideBlank = $this->getOption('hideBlank');
		
		$filterFieldValueHere = $originalValue;
		if( !empty($context['itemId']) ) {
			$itemInfo = TikiLib::lib('trk')->get_tracker_item( $context['itemId'] );
			if( !empty($itemInfo) && !empty($itemInfo[$filterFieldIdHere]) ) {
				$filterFieldValueHere = $itemInfo[$filterFieldIdHere];
			}
		}
		
		if( $filterFieldIdHere == $this->getConfiguration('fieldId') )
			return tr('*** ERROR: Field ID (This tracker) cannot be the same: %0 ***', $filterFieldIdHere);

		if( !TikiLib::lib('trk')->get_tracker_field($listFieldIdThere) )
			return tr('*** ERROR: Field %0 not found ***', $listFieldIdThere);
		
		TikiLib::lib('header')->add_jq_onready(
			'
var dilIsInit_'. $insertId. ' = false;

$("body").on("change", "input[name=ins_' . $filterFieldIdHere . '], select[name=ins_' . $filterFieldIdHere . ']", function(e, val) {
	if (val && val == "' . $insertId . '" && dilIsInit_'. $insertId. ') {
		return; // on init, only fire one time per select trigger eventhandler. otherwise each init would trigger all prev. registered handlers
	}
	dilIsInit_'. $insertId. ' = true;
	$.getJSON(
		"tiki-tracker_http_request.php",
		{
			filterFieldIdHere: ' . $filterFieldIdHere . ',
			trackerIdThere: ' . $trackerIdThere . ',
			listFieldIdThere: ' . $listFieldIdThere . ',
			filterFieldIdThere: ' . $filterFieldIdThere . ',
			statusThere: "' . $statusThere . '",
			mandatory: "' . $isMandatory . '",
			insertId: "' . $insertId . '",  // need to pass $insertId in case we have more than one field bound to the same eventsource
			originalValue:  "' . $originalValue . '",
			hideBlank: '.intval($hideBlank).',
			filterFieldValueHere: $(this).val() // We need the field value for the fieldId filterfield for the item $(this).val
		},
		
		// callback
		function(data, status) {
			if (data && data.request && data.response) {
				targetDDL = "select[name=" + data.request.insertId + "]";
				$ddl = $(targetDDL);
				$ddl.empty();
				
				var v, l;
				response = data.response;
				$.each( response, function (i,data) {
					if (data && data.length > 1) {
						v = data[0];
						l = data[1];
					} else {
						v = ""
						l = "";
					}
					$ddl.append(
						$("<option/>")
							.val(v)
							.text(l)
					);
				}); // each
					
					if (data.request.originalValue) {
					$ddl.val(data.request.originalValue);
				}
			}

			if (jqueryTiki.chosen) {
				$ddl.trigger("chosen:updated");
			}
			$ddl.trigger("change");
		} // callback
	);  // getJSON
});
$("input[name=ins_' . $filterFieldIdHere . '], select[name=ins_' . $filterFieldIdHere . ']").trigger("change", "'. $insertId. '"); // closure

if( $("input[name=ins_' . $filterFieldIdHere . '], select[name=ins_' . $filterFieldIdHere . ']").length == 0 ) {
	// inline edit fix
	$("<input type=\"hidden\" name=\"ins_' . $filterFieldIdHere . '\">").val(' . json_encode($filterFieldValueHere) . ').insertBefore("select[name=' . $insertId . ']").trigger("change");
}
		'
		); // add_jq_onready

		return '<select class="form-control" name="' . $insertId . '"></select>';

	}
	
	

	
	// If you make changes here check also tiki-tracker_http_request.php as long as it is not integrated in ajax-services
	// @TODO Move parts of this to getFieldData()
	public function renderInnerOutput($context = array())
	{
		$trklib = TikiLib::lib('trk');
		// remote tracker and remote field
		$trackerIdThere = $this->getOption('trackerId');
		$definition = Tracker_Definition::get($trackerIdThere);
		$listFieldIdThere = $this->getOption('listFieldIdThere');
		$listFieldThere = $definition->getField($listFieldIdThere);
		
		// $listFieldThere above does not return any value for fieldtype category. Maybe a bug?
		if (!isset($listFieldThere)) {
			$listFieldThere = $trklib->get_tracker_field($listFieldIdThere);
		}
		
		if( empty($listFieldThere) )
			return tr('*** ERROR: Field %0 not found ***', $listFieldIdThere);
		
		$remoteItemId = $this->getValue();
		$itemInfo = $trklib->get_tracker_item($remoteItemId);
		
		switch ($listFieldThere['type']) {
			// e = category
			// d = dropdown
			case 'e':
			case 'd':
				//$listFieldThere = array_merge($listFieldThere, array('value' => $remoteItemId));
				$handler = $trklib->get_field_handler($listFieldThere, $itemInfo);
				// array selected_categories etc.
				$valueField = $handler->getFieldData();
				// for some reason, need to apply the values back, otherwise renderOutput does not return a value - bug or intended?
				$listFieldThere = array_merge($listFieldThere, $valueField);
				$handler = $trklib->get_field_handler($listFieldThere, $itemInfo);
				$context = array('showlinks' => 'n');
				$labelField = $handler->renderOutput($context);
				return $labelField;
			break;

			// r = item-link requires $listFieldThere = array_merge($listFieldThere, array('value' => $remoteItemId));
			case 'r':
				$listFieldThere = array_merge($listFieldThere, array('value' => $remoteItemId));
				$handler = $trklib->get_field_handler($listFieldThere, $itemInfo);
				// do not inherit showlinks settings from remote items.
				$context = array('showlinks' => 'n');
				$labelField = $handler->renderOutput($context);
				return $labelField;
			break;
			
			//l = item-list
			case 'l':
				// show selected item of that list - requires match in tiki-tracker_http_request.php
				//$listFieldThere = array_merge($listFieldThere, array('value' => $remoteItemId));
				$handler = $trklib->get_field_handler($listFieldThere);
				$displayFieldIdThere = $handler->getOption('displayFieldIdThere');
				// do not inherit showlinks settings from remote items.
				$context = array('showlinks' => 'n');
				foreach ($displayFieldIdThere as $displayFieldId) {
					$displayField = $trklib->get_tracker_field($displayFieldId);
					// not shure why this is needed - and only for some fieldtypes? 
					//renderOutput() in abstract checks only $this->definition['value'], not $this->itemdata
					$displayField = array_merge($displayField, array('value' => $itemInfo[$displayFieldId]));
					$handler = $trklib->get_field_handler($displayField, $itemInfo);
					$labelFields[] = $handler->renderOutput($context);
				}
				$labelField = implode(' ', $labelFields);
				return $labelField;
			break;
		
			
			// i.e textfield - requires $listFieldThere = array_merge($listFieldThere, array('value' => $itemInfo[$listFieldIdThere]));
			default:
				$listFieldThere = array_merge($listFieldThere, array('value' => $itemInfo[$listFieldIdThere]));
				$handler = $trklib->get_field_handler($listFieldThere, $itemInfo);
				// do not inherit showlinks settings from remote items.
				$context = array('showlinks' => 'n');
				$labelField = $handler->renderOutput($context);
				return $labelField;
			break;
		}
	}

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$item = $this->getValue();
		$baseKey = $this->getBaseKey();

		$out = array(
			$baseKey => $typeFactory->identifier($item),
			"{$baseKey}_text" => $typeFactory->sortable($this->renderInnerOutput()),
		);
		return $out;
	}

	function getItemList()
	{
		return TikiLib::lib('trk')->get_all_items(
			$this->getOption('trackerId'),
			$this->getOption('listFieldIdThere'),
			$this->getOption('statusThere', 'opc')
		);
	}
}

