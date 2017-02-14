<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: renderer_character_inline.php 57967 2016-03-17 20:06:16Z jonnybradley $

/**
 * "Inline" character diff renderer.
 *
 * This class renders the diff in "inline" format by characters.
 *
 * @package Text_Diff
 */
class Text_Diff_Renderer_character_inline extends Tiki_Text_Diff_Renderer
{
    var $orig;
    var $final;

    function __construct($context_lines = 0)
    {
        $this->_leading_context_lines = $context_lines;
        $this->_trailing_context_lines = $context_lines;
        $this->diff = "";
        $this->change = "";
    }
    
    function _startDiff()
    {
    }

    function _endDiff()
    {
        return array($this->diff, $this->change);
    }

    function _blockHeader($xbeg, $xlen, $ybeg, $ylen)
    {
    }

    function _startBlock($header)
    {
        echo $header;
    }

    function _endBlock()
    {
    }

    function _getChange($lines)
    {
	return str_replace("<br />", "↵<br />", join("", $lines));
    }

    function _lines($type, $lines, $prefix = '')
    {
		if ($type == 'context') {
			$this->diff .= join("", $lines);
		} elseif ($type == 'added' || $type == 'change-added') {
			$t = $this->_getChange($lines);
	        if (!empty($t))
	            $this->diff .= "<span class='diffadded'>$t</span>";
		} elseif ($type == 'deleted' || $type == 'change-deleted') {
			$t = $this->_getChange($lines);
	        if (!empty($t))
	            $this->diff .= "<span class='diffinldel'>$t</span>";
		} elseif ($type == 'changed') {
			$t = $this->_getChange($lines[0]);
			if (!empty($t))
				$this->diff .= "<span class='diffinldel'>$t</span>";
			$t = $this->_getChange($lines[1]);
			if (!empty($t))
				$this->diff .= "<span class='diffadded'>$t</span>";
		}
    }

    function _context($lines)
    {
        $this->_lines('context', $lines);
    }

    function _added($lines, $changemode = FALSE)
    {
	if (!$this->change)
		$this->change = "added";
	if ($this->change != "added")
		$this->change = "changed";

        if ($changemode) {
        	$this->_lines('change-added', $lines, '+');
        } else {
        	$this->_lines('added', $lines, '+');
        }
    }

    function _deleted($lines, $changemode = FALSE)
    {
	if (!$this->change)
		$this->change = "deleted";
	if ($this->change != "deleted")
		$this->change = "changed";

        if ($changemode) {
        	$this->_lines('change-deleted', $lines, '-');
        } else {
	        $this->_lines('deleted', $lines, '-');
        }
    }

    function _changed($orig, $final)
    {
	$this->change = 'changed';
	$this->_lines('changed', array($orig, $final), '*');
    }

}
