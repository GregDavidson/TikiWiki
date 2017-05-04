<?php
// NGender Nicer Logging:
function var_log( $val, $var_name = '', $file = '', $line = '', $func_name='', $class_name='' ) {
  $context = ''
					 . ( $file === '' ? '' : ( $file . ' ' ) )
					 . ( $line === '' ? '' : ( $line . ' ' ) )
					 . ( $class_name === '' ? '' : ( $class_name . '->' ) )
					 . ( $func_name === '' ? '' : ( $func_name . '() ' ) )
					 . ( $var_name === '' ? '' : ( $var_name . ': ' ) );
	ob_start();                    // start capture
	var_dump( $val );           // dump value with type info
	$lines = preg_split("/\r\n|\n|\r/", ob_get_contents());
	ob_end_clean();                // end capture
	// $lines = preg_split("/\r\n|\n|\r/", var_export($val, 1));
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
?>