<?php
// NGender Nicer Logging:

// requires php5.6 for ... syntax
// return first non-empty val followed by delimiter
function delim_vals($delim, ...$vals) {
	foreach ( $vals as $val ) {
		if ($val !== '') return $val . $delim;
	}
	return '';
}

function maybe_key_array($key, $ra, $dfalt='') {
	return array_key_exists($key, $ra) ? $ra[$key] : $dfalt;
}

// create a context suitable for a log message
// prefer explicit $file & $line if present
// pull available context from stack $frame
function log_context( $frame, $file = '', $line = '' ) {
	return delim_vals(' ', $file, maybe_key_array('file', $frame))
		. delim_vals(' ', $line, maybe_key_array('line', $frame))
		. delim_vals(
			maybe_key_array('type', $frame, '~>'),
			maybe_key_array('class', $frame) )
		. maybe_key_array('function', $frame);
}

// print a value to the error log along with context
// - maybe change the name to log_value()?
// prefer explicit context arguments
// pull additional context from stack frame
function var_log( $value, $label = '', $file = '', $line = '' ) {
	$stack = debug_backtrace(true, 2);
	$frame = ( ! is_array($stack) || count($stack) > 0 )
				 ? array()
				 : count($stack) > 1 ? $stack[1] : $stack[0];
  $context = log_context( $frame, $file, $line )
					 . ' ' . delim_vals(': ', $label);
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

// print a stack dump to the error log
// - maybe change the name to log_stack()?
// only show scalar arguments
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
		error_log( log_context($frame) . $arglist );
	}
}
?>