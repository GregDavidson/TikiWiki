<?php
// NGender Nicer Logging:

// requires php5.6 for ... varargs syntax
// qualifying values are numbers or non-empty strings
// returns first qualifying value followed by delimiter
function delim_first($delim, ...$vals) {
	foreach ( $vals as $val ) {
	  if (is_numeric($val)) {
	    $val = (string) $val;
	  }
	  if ( is_string($val) && $val !== '' ) {
	    return $val . $delim;
	  }
	}
	return '';
}

function maybe_key_array($key, $ra, $dfalt='') {
  return ( isset($ra) && is_array($ra) && array_key_exists($key, $ra) ) ? $ra[$key] : $dfalt;
}

// create context string for log message
// pull available context from stack $frame
// prefer explicit $file & $line if present
// context starts with '| '
function context_log_str( $frame, $file = '', $line = '', $func_delim = '' ) {
  return '| '
		. delim_first(' ', $file, maybe_key_array('file', $frame))
    . delim_first(' ', $line, maybe_key_array('line', $frame))
    . delim_first(
									maybe_key_array('type', $frame, '~>'),
									maybe_key_array('class', $frame) )
    . delim_first( $func_delim, maybe_key_array('function', $frame) );
}

// print a value to the error log along with context
// - maybe change the name to log_value()?
// pull available context from stack $frame
// prefer explicit $file & $line if present
function var_log( $value, $label = '', $file = '', $line = '' ) {
  $stack = debug_backtrace(true, 2);
  $frame = ( is_array($stack) && count($stack) > 1 ) ? $stack[1] : array();
  $context = context_log_str( $frame, $file, $line, '()' )
    . ' ' . delim_first(': ', $label);
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

// convert argument to short debug string
$arg_log_str = function($arg) {
  $result = gettype($arg);
  if (is_string($arg)) {
    if (strlen($arg) < 10) {
      $result .= ": '$arg'";
    } else {
      $result .= ": '" . substr($arg, 0, 10) . "'...";
    }
  } elseif (is_numeric($arg)) {
    $result .= ': ' . $arg;
  } elseif (is_bool($arg)) {
    $result .= ': ' . ( $arg ? "true" : "false" );
  } elseif (is_object($arg)) {
    $result .= ' ' . get_class($arg);
  } elseif (is_array($arg)) {
    $result .= ' ' . count($arg);
  }
  return $result;
};

// print a stack dump to the error log
// - maybe change the name to log_stack()?
// only show scalar arguments
function stack_log( $num_frames = 2 ) {
  global $arg_log_str;
  $stack = debug_backtrace(true, $num_frames);
  $stack_len = count($stack);
  if ( $num_frames <= 0 || $num_frames > $stack_len ) {
    $num_frames = $stack_len;
  }
  for ($level = 1; $level < $num_frames; ++$level) {
    $frame = $stack[$level];
    $arg_list = array_map($arg_log_str, $frame['args']);
    error_log( context_log_str($frame) . '(' . implode(", ", $arg_list) . ')' );
  }
}
?>