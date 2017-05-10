<?php
// NGender Nicer Logging:

function stack_context( $frame ) {
	$context = '';
	if (array_key_exists('file', $frame)) {
		$context .= ( $frame['file'] . ' ' );
	}
	if (array_key_exists('line', $frame)) {
		$context .= ( $frame['line'] . ' ' );
	}
	if (array_key_exists('class', $frame)) {
		$context .= $frame['class'];
		if (array_key_exists('type', $frame)) {
			$context .= ( $frame['type'] );
		} else {
			$context .= ( '~>' );
		}
		$context .= $frame['function'];
	}
	return $context;
}

function var_log( $value, $label = '' ) {
  $context = stack_context(debug_backtrace(true, 2)[1])
		. ' ' . ($label === '' ? '' : ($label . ': '));
	ob_start();                    // start capture
	var_dump( $value );           // dump value with type info
	$lines = preg_split("/\r\n|\n|\r/", ob_get_contents());
	ob_end_clean();                // end capture
	// $lines = preg_split("/\r\n|\n|\r/", var_export($value, 1));
	$key_line = '';
	foreach ( $lines as $line ) {
		if ( $line === '' ) { continue; }
		if ( $key_line === '' && substr($line, -2) == '=>' ) {
			$key_line = $line; continue;
		}
		if ($key_line == '') {
			error_log( $context . $line ); continue;
		}
		$trimline = ltrim($line);
		if ( strspn($key_line, ' ') === strspn($line, ' ') && strspn($trimline, '}') === 0 ) {
			// same indent and $line doesn't end a block so combine them:
			error_log( $context . $key_line . $trimline );
		} else {
			error_log( $context . $key_line );
			error_log( $context . $line );
		}
		$key_line = '';
	}
}

function stack_log( $num_frames = 2 ) {
	$stack = debug_backtrace(true, $num_frames);
	$stack_len = count($stack);
	if ( $num_frames <= 0 || $num_frames > $stack_len ) {
		$num_frames = $stack_len;
	}
	for ($level = 1; $level < $num_frames; $level++) {
		$frame = $stack[$level];
		$delim = '(';
		$num_args = count($frame['args']);
		for ($arg = 0; $arg < $num_args; $arg++) {
			$arglist = $delim;
			$arg_val = $frame['args'][$arg];
			if (is_string($arg_val)) {
				$arglist .= $arg_val;
			}
			$delim = ', ';
		}
		$arglist .= ')';
		error_log( stack_context($frame) . $arglist );
	}
}
?>