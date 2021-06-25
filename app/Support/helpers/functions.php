<?php
function absint($maybeInt){
    return abs(intval($maybeInt));
}
/**
 * Unserialize value only if it was serialized.
 *
 * @since 2.0.0
 *
 * @param string $original Maybe unserialized original, if is needed.
 * @return mixed Unserialized data can be any type.
 */
function maybe_unserialize( $original ) {
    if ( is_serialized( $original ) ) // don't attempt to unserialize data that wasn't serialized going in
        return @unserialize( $original );
    return $original;
}

/**
 * Check value to find if it was serialized.
 *
 * If $data is not an string, then returned value will always be false.
 * Serialized data is always a string.
 *
 * @since 2.0.5
 *
 * @param string $data   Value to check to see if was serialized.
 * @param bool   $strict Optional. Whether to be strict about the end of the string. Default true.
 * @return bool False if not serialized and true if it was.
 */
function is_serialized( $data, $strict = true ) {
    // if it isn't a string, it isn't serialized.
    if ( ! is_string( $data ) ) {
        return false;
    }
    $data = trim( $data );
    if ( 'N;' == $data ) {
        return true;
    }
    if ( strlen( $data ) < 4 ) {
        return false;
    }
    if ( ':' !== $data[1] ) {
        return false;
    }
    if ( $strict ) {
        $lastc = substr( $data, -1 );
        if ( ';' !== $lastc && '}' !== $lastc ) {
            return false;
        }
    } else {
        $semicolon = strpos( $data, ';' );
        $brace     = strpos( $data, '}' );
        // Either ; or } must exist.
        if ( false === $semicolon && false === $brace )
            return false;
        // But neither must be in the first X characters.
        if ( false !== $semicolon && $semicolon < 3 )
            return false;
        if ( false !== $brace && $brace < 4 )
            return false;
    }
    $token = $data[0];
    switch ( $token ) {
        case 's' :
            if ( $strict ) {
                if ( '"' !== substr( $data, -2, 1 ) ) {
                    return false;
                }
            } elseif ( false === strpos( $data, '"' ) ) {
                return false;
            }
        // or else fall through
        case 'a' :
        case 'O' :
            return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
        case 'b' :
        case 'i' :
        case 'd' :
            $end = $strict ? '$' : '';
            return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
    }
    return false;
}
/**
 * Serialize data, if needed.
 *
 * @since 2.0.5
 *
 * @param string|array|object $data Data that might be serialized.
 * @return mixed A scalar data
 */
function maybe_serialize( $data ) {
    if ( is_array( $data ) || is_object( $data ) )
        return serialize( $data );

    return $data;
}

function parse_id_list( $list , $int=false) {
    $list=parse_number_list($list);
    if($int){
        $list=array_map('absint',$list);
    }
    return array_unique($list);
}
function parse_number_list($list){
    if(!$list){
        return [];
    }
    if ( !is_array($list) )
        $list = preg_split('/[\s,]+/', $list);
    $list=array_filter($list,function($item){
        if(empty($item)){
            return false;
        }
        if(preg_match('#[^0-9]#',$item)){
            return false;
        }
        return $item;
    });
    return $list;
}

function qc_parse_str( $string, &$array ) {
    parse_str( $string, $array );
    if ( get_magic_quotes_gpc() )
        $array = stripslashes_deep( $array );
}

function stripslashes_deep( $value ) {
    return map_deep( $value, function( $value ) {
        return is_string( $value ) ? stripslashes( $value ) : $value;
    } );
}

function la_parse_args($args, $defaults = '' ) {
    if ( is_object( $args ) )
        $r = get_object_vars( $args );
    elseif ( is_array( $args ) )
        $r =& $args;
    else
        qc_parse_str( $args, $r );

    if ( is_array( $defaults ) )
        return array_merge( $defaults, $r );
    return $r;
}

function asset_url($path,$package='admin'){
	if(app('url')->isValidUrl($path)){
		return $path;
	}
	$path='/assets/'.$package.'/'.trim($path,'/');
	return asset(ltrim($path,'/'));
}
function vendor_asset($path,$package){
    if(app('url')->isValidUrl($path)){
        return $path;
    }
    $path='/vendor/'.$package.'/'.trim($path,'/');
    return asset(ltrim($path,'/'));
}

function customer_asset($path=''){
	return asset_url($path,'my');
}

function array_translate_keys($array,$trans){
    if(!is_array($trans)||!is_array($array)){
        return $array;
    }
    foreach ($trans as $from=>$to){
        if($from!=$to){
            if(array_key_exists($from,$array)){
                $array[$to]=$array[$from];
                unset($array[$from]);
            }
        }
    }
    return $array;

}

/**
 * Check if we are in admin area
 * @return bool
 */
function is_admin(){
    $app=app();
    if($app->resolved('is_admin'))
        return $app->make('is_admin');
    return false;
}

/**
 * Convert number of bytes largest unit bytes will fit into.
 *
 * It is easier to read 1 KB than 1024 bytes and 1 MB than 1048576 bytes. Converts
 * number of bytes to human readable number by taking the number of that unit
 * that the bytes will go into it. Supports TB value.
 *
 * Please note that integers in PHP are limited to 32 bits, unless they are on
 * 64 bit architecture, then they have 64 bit size. If you need to place the
 * larger size then what PHP integer type will hold, then use a string. It will
 * be converted to a double, which should always have 64 bit length.
 *
 * Technically the correct unit names for powers of 1024 are KiB, MiB etc.
 *
 * @since 2.3.0
 *
 * @param int|string $bytes    Number of bytes. Note max integer size for integers.
 * @param int        $decimals Optional. Precision of number of decimal places. Default 0.
 * @return string|false False on failure. Number string on success.
 */
function size_format( $bytes, $decimals = 0 ) {
	$quant = array(
		'TB' => TB_IN_BYTES,
		'GB' => GB_IN_BYTES,
		'MB' => MB_IN_BYTES,
		'KB' => KB_IN_BYTES,
		'B'  => 1,
	);

	if ( 0 === $bytes ) {
		return number_format( 0, $decimals ) . ' B';
	}

	foreach ( $quant as $unit => $mag ) {
		if ( doubleval( $bytes ) >= $mag ) {
			return number_format( $bytes / $mag, $decimals ) . ' ' . $unit;
		}
	}

	return false;
}

function map_deep( $value, $callback ) {
    if ( is_array( $value ) ) {
        foreach ( $value as $index => $item ) {
            $value[ $index ] = map_deep( $item, $callback );
        }
    } elseif ( is_object( $value ) ) {
        $object_vars = get_object_vars( $value );
        foreach ( $object_vars as $property_name => $property_value ) {
            $value->$property_name = map_deep( $property_value, $callback );
        }
    } else {
        $value = call_user_func( $callback, $value );
    }

    return $value;
}
/**
 * Perform a deep string replace operation to ensure the values in $search are no longer present
 *
 * Repeats the replacement operation until it no longer replaces anything so as to remove "nested" values
 * e.g. $subject = '%0%0%0DDD', $search ='%0D', $result ='' rather than the '%0%0DD' that
 * str_replace would return
 *
 * @since 2.8.1
 * @access private
 *
 * @param string|array $search  The value being searched for, otherwise known as the needle.
 *                              An array may be used to designate multiple needles.
 * @param string       $subject The string being searched and replaced on, otherwise known as the haystack.
 * @return string The string with the replaced svalues.
 */
function _deep_replace( $search, $subject ) {
    $subject = (string) $subject;

    $count = 1;
    while ( $count ) {
        $subject = str_replace( $search, '', $subject, $count );
    }

    return $subject;
}

/**
 * Set the mbstring internal encoding to a binary safe encoding when func_overload
 * is enabled.
 *
 * When mbstring.func_overload is in use for multi-byte encodings, the results from
 * strlen() and similar functions respect the utf8 characters, causing binary data
 * to return incorrect lengths.
 *
 * This function overrides the mbstring encoding to a binary-safe encoding, and
 * resets it to the users expected encoding afterwards through the
 * `reset_mbstring_encoding` function.
 *
 * It is safe to recursively call this function, however each
 * `mbstring_binary_safe_encoding()` call must be followed up with an equal number
 * of `reset_mbstring_encoding()` calls.
 *
 *
 * @see reset_mbstring_encoding()
 *
 * @staticvar array $encodings
 * @staticvar bool  $overloaded
 *
 * @param bool $reset Optional. Whether to reset the encoding back to a previously-set encoding.
 *                    Default false.
 */
function mbstring_binary_safe_encoding( $reset = false ) {
	static $encodings = array();
	static $overloaded = null;

	if ( is_null( $overloaded ) )
		$overloaded = function_exists( 'mb_internal_encoding' ) && ( ini_get( 'mbstring.func_overload' ) & 2 );

	if ( false === $overloaded )
		return;

	if ( ! $reset ) {
		$encoding = mb_internal_encoding();
		array_push( $encodings, $encoding );
		mb_internal_encoding( 'ISO-8859-1' );
	}

	if ( $reset && $encodings ) {
		$encoding = array_pop( $encodings );
		mb_internal_encoding( $encoding );
	}
}

/**
 * Reset the mbstring internal encoding to a users previously set encoding.
 *
 * @see mbstring_binary_safe_encoding()
 *
 * @since 3.7.0
 */
function reset_mbstring_encoding() {
	mbstring_binary_safe_encoding( true );
}

/**
 * Test if a give filesystem path is absolute.
 *
 * For example, '/foo/bar', or 'c:\windows'.
 *
 *
 * @param string $path File path.
 * @return bool True if path is absolute, false is not absolute.
 */
function path_is_absolute( $path ) {
	/*
	 * This is definitive if true but fails if $path does not exist or contains
	 * a symbolic link.
	 */
	if ( realpath($path) == $path )
		return true;

	if ( strlen($path) == 0 || $path[0] == '.' )
		return false;

	// Windows allows absolute paths like this.
	if ( preg_match('#^[a-zA-Z]:\\\\#', $path) )
		return true;

	// A path starting with / or \ is absolute; anything else is relative.
	return ( $path[0] == '/' || $path[0] == '\\' );
}

/**
 * Return relative path to public directory
 *
 * @param string $path Full path to the file.
 * @return string Relative path on success, unchanged path on failure.
 */
function relative_public_path($path){
	$public_path=public_path();
	if(0===strpos($path,$public_path)){
		$path = str_replace( $public_path, '', $path );
		$path=str_replace('\\','/',$path);
		$path=preg_replace('#/+#','/',$path);
		$path = ltrim( $path, '/' );
	}
	return $path;
}

function skipSchedule(){
    $input=new Symfony\Component\Console\Input\ArgvInput();
    $command=$input->getFirstArgument();
    if(!$command){
        return true;
    }
    if(strpos($command,'schedule')===false){
        return true;
    }
    return false;
}
function is_installing(){
    if(!defined('ARTISAN_BINARY')){
        return false;
    }
    $input=new Symfony\Component\Console\Input\ArgvInput();
    $command=$input->getFirstArgument();
    if($command==='migrate' || $command==='make:migration'){
        return true;
    }
    if($command==='package:discover'){
        return true;
    }
    return false;
}

function retrieve(Closure $callback,$maxTries=5,$delay=5){
    $i=0;
    $log=function($message){
        $output=app('command_output');
        if($output instanceof \Symfony\Component\Console\Output\Output){
            $output->writeln($message);
        }else{
            echo $message.PHP_EOL;
        }
    };
    while(1) {
        try {
            return $callback();
        }
        catch (Exception $e) {
        	if($e->getPrevious() instanceof \Facebook\Exceptions\FacebookAuthenticationException){
        		throw $e;
			}
            if ($i++ >= $maxTries) {
                $log('Reached '.$maxTries.' times of retrieve');
                throw $e;
            }
            $log('Failed '.$i.': '.$e->getMessage());
            sleep($i*$delay);
        }
    }
    return false;
}

/**
 * @return \App\Model\Site
 */
function site(){
    return app(\App\Model\Site::class);
}
