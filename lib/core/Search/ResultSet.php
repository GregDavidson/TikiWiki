<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ResultSet.php 58770 2016-06-02 16:34:23Z patrick-proulx $

class Search_ResultSet extends ArrayObject implements JsonSerializable
{
	private $count;
	private $estimate;
	private $offset;
	private $maxRecords;

	private $highlightHelper;
	private $filters = array();
	private $id;
	private $tsOn;
	private $tsettings;

	public static function create($list)
	{
		if ($list instanceof self) {
			return $list;
		} else {
			return new self($list, count($list), 0, count($list));
		}
	}

	function __construct($result, $count, $offset, $maxRecords)
	{
		parent::__construct($result);

		$this->count = $count;
		$this->estimate = $count;
		$this->offset = $offset;
		$this->maxRecords = $maxRecords;
		$this->checkNestedObjectPerms();
	}

	function replaceEntries($list)
	{
		$return = new self($list, $this->count, $this->offset, $this->maxRecords);
		$return->estimate = $this->estimate;
		$return->filters = $this->filters;
		$return->highlightHelper = $this->highlightHelper;
		$return->id = $this->id;
		$return->tsOn = $this->tsOn;
		$return->count = $this->count;
		$return->tsettings = $this->tsettings;

		return $return;
	}

	function setHighlightHelper(Zend\Filter\FilterInterface $helper)
	{
		$this->highlightHelper = $helper;
	}

	function setEstimate($estimate)
	{
		$this->estimate = (int) $estimate;
	}

	function setId($id)
	{
		$this->id = $id;
	}

	function getId()
	{
		return $this->id;
	}

	function setTsOn($tsOn)
	{
		$this->tsOn = $tsOn;
	}

	function setTsSettings($tsettings)
	{
		$this->tsettings = $tsettings;
	}

	function getTsOn()
	{
		return $this->tsOn;
	}

	function getTsSettings()
	{
		return $this->tsettings;
	}

	function getEstimate()
	{
		return $this->estimate;
	}

	function getMaxRecords()
	{
		return $this->maxRecords;
	}

	function setMaxResults($max)
	{
		$current = $this->exchangeArray(array());
		$this->maxRecords = $max;
		$this->exchangeArray(array_slice($current, 0, $max));
	}

	function getOffset()
	{
		return $this->offset;
	}

	function count()
	{
		return $this->count;
	}

	function highlight($content)
	{
		if ($this->highlightHelper) {
			// Build the content string based on heuristics
			$text = '';
			foreach ($content as $key => $value) {
				if ($key != 'object_type' // Skip internal values
				 && $key != 'object_id'
				 && $key != 'parent_object_type'
				 && $key != 'parent_object_id'
				 && $key != 'relevance'
				 && $key != 'score'
				 && $key != 'url'
			     && $key != 'title'
			     && $key != 'title_initial'
			     && $key != 'title_firstword'
			     && $key != 'description'
				 && ! empty($value) // Skip empty
				 && ! is_array($value) // Skip arrays, multivalues fields are not human readable
				 && ! preg_match('/token[a-z]{8,}/', $value)	// tokens
				 && ! preg_match('/\d{4}-\d{2}-\d{2} \d{2}\:\d{2}\:\d{2}/', $value)	// dates
				 && ! preg_match('/^[\w-]+$/', $value)) { // Skip anything that looks like a single token
					$text .= "\n$value";
				}
			}

			if (! empty($text)) {
				return $this->highlightHelper->filter($text);
			}
		}
	}

	function hasMore()
	{
		return $this->count > $this->offset + $this->maxRecords;
	}

	function getFacet(Search_Query_Facet_Interface $facet)
	{
		foreach ($this->filters as $filter) {
			if ($filter->isFacet($facet)) {
				return $filter;
			}
		}
	}

	function getFacets()
	{
		return $this->filters;
	}

	function addFacetFilter(Search_ResultSet_FacetFilter $facet)
	{
		$this->filters[$facet->getName()] = $facet;
	}

	function groupBy($field, array $collect = array())
	{
		$out = array();
		foreach ($this as $entry) {
			if (! isset($entry[$field])) {
				$out[] = $entry;
			} else {
				$value = $entry[$field];
				if (! isset($out[$value])) {
					$newentry = $entry;
					$newentry[$field] = array_fill_keys($collect, array());
					$out[$value] = $newentry;
				}

				foreach ($collect as $key) {
					if (isset($entry[$key])) {
						$out[$value][$field][$key][] = $entry[$key];
						$out[$value][$field][$key] = array_unique($out[$value][$field][$key]);
					}
				}
			}
		}

		$this->exchangeArray($out);
	}

	function applyTransform(callable $transform)
	{
		foreach ($this as & $entry) {
			$entry = $transform($entry);
		}
	}
	/**  When relations have indexed relation objects, remove them from the resultset if user doesn't have
	 * proper permissions */
	function checkNestedObjectPerms(){
		global $user;
		$user_groups = array_keys(TikiLib::lib('user')->get_user_groups_inclusion($user));
		if (empty($user_groups)) {
			$user_groups = ['Anonymous'];
		}
		foreach($this as &$item){//for each element in resultset
			if (isset($item['relation_objects'])){
				foreach ($item['relation_objects'] as $key => $obj) {
					$in_group = array_intersect($obj->allowed_groups,$user_groups);
					$in_user = in_array($user, $obj->allowed_users);
					if (!$in_group && !$in_user){
						unset($item['relation_objects'][$key]);
					}
				}
				$item['relation_objects'] = array_values($item['relation_objects']); //rebase keys
			}
		}
	}

	function jsonSerialize()
	{
		return [
			'count' => $this->count,
			'offset' => $this->offset,
			'maxRecords' => $this->maxRecords,
			'result' => array_values($this->getArrayCopy()),
		];
	}
}

