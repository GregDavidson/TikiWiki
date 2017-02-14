<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-graph_formula.php 57957 2016-03-17 19:58:54Z jonnybradley $

require_once('tiki-setup.php');
require_once('lib/graph-engine/graph.multiline.php');
require_once('lib/graph-engine/gd.php');
require_once('lib/graph-engine/pdflib.php');

// List of valid functions
$valid = array(
	'abs',
	'acos',
	'acosh',
	'asin',
	'asinh',
	'atan2',
	'atan',
	'atanh',
	'ceil',
	'cos',
	'cosh',
	'deg2rad',
	'exp',
	'expm1',
	'floor',
	'fmod',
	'hypot',
	'log10',
	'log1p',
	'log',
	'max',
	'min',
	'pi',
	'pow',
	'rad2deg',
	'round',
	'sin',
	'sinh',
	'sqrt',
	'tan',
	'tanh'
);

/**
 * @param $formula
 * @return string
 */
function convert_formula( $formula )
{
	global $valid;

	// Stripping all quotes
	$chars = array( '`', "'", '"', '&', '[', ']', '$', '{', '}' );
	$formula = str_replace($chars, array_fill(0, count($chars), ''), $formula);

	// Make sure only valid functions are used
	preg_match_all('/([a-z0-9_]+)/i', $formula, $out, PREG_PATTERN_ORDER);
	foreach ( $out[0] as $match )
		if ( !is_numeric($match) && !in_array(strtolower($match), $valid) && $match !== 'x' )
			die( "Invalid function call {$match}" );

	// Replace spaces for commas
	$formula = preg_replace('/\s+/', ', ', $formula);

	$formula = str_replace('x', '$x', $formula);

	return create_function('$x', "return $formula;");
}

$access->check_permission('feature_sheet');

if ( !( is_numeric($_GET['w'])
	&& is_numeric($_GET['h'])
	&& is_numeric($_GET['s'])
	&& $_GET['s'] <= 500 && $_GET['s'] > 0
	&& is_numeric($_GET['min'])
	&& is_numeric($_GET['max'])
	&& is_array($_GET['f'])
	&& $_GET['min'] < $_GET['max']
	&& $_GET['w'] >= 100
	&& $_GET['h'] >= 100 )
)
	die;

switch ( $_GET['t'] ) {
	case 'png':
		$renderer = new GD_GRenderer($_GET['w'], $_GET['h']);
		break;
	case 'pdf':
		$renderer = new PDFLib_GRenderer($_GET['p'], $_GET['o']);
		break;
	default:
		die;
}

$graph = new MultilineGraphic;
$graph->setTitle($_GET['title']);

$size = ($_GET['max'] - $_GET['min']) / $_GET['s'];

$data = array();
foreach ( array_values($_GET['f']) as $key=>$formula) {
	$formula = convert_formula($formula);

	$data['x'] = array();
	$data['y'.$key] = array();

	for ( $x = $_GET['min']; $_GET['max'] > $x; $x += $size ) {
		$data['x'][] = $x;
		$data['y'.$key][] = $formula($x);
	}
}

$graph->setData($data);
$graph->draw($renderer);

$renderer->httpOutput("graph.{$_GET['t']}");
