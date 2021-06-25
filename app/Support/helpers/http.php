<?php
/**
 * Navigates through an array, object, or scalar, and encodes the values to be used in a URL.
 *
 * @since 2.2.0
 *
 * @param mixed $value The array or string to be encoded.
 * @return mixed $value The encoded value.
 */
function urlencode_deep( $value ) {
    return map_deep( $value, 'urlencode' );
}


function add_query_arg() {
    $args = func_get_args();
    if ( is_array( $args[0] ) ) {
        if ( count( $args ) < 2 || false === $args[1] )
            $uri = $_SERVER['REQUEST_URI'];
        else
            $uri = $args[1];
    } else {
        if ( count( $args ) < 3 || false === $args[2] )
            $uri = $_SERVER['REQUEST_URI'];
        else
            $uri = $args[2];
    }

    if ( $frag = strstr( $uri, '#' ) )
        $uri = substr( $uri, 0, -strlen( $frag ) );
    else
        $frag = '';

    if ( 0 === stripos( $uri, 'http://' ) ) {
        $protocol = 'http://';
        $uri = substr( $uri, 7 );
    } elseif ( 0 === stripos( $uri, 'https://' ) ) {
        $protocol = 'https://';
        $uri = substr( $uri, 8 );
    } else {
        $protocol = '';
    }

    if ( strpos( $uri, '?' ) !== false ) {
        list( $base, $query ) = explode( '?', $uri, 2 );
        $base .= '?';
    } elseif ( $protocol || strpos( $uri, '=' ) === false ) {
        $base = $uri . '?';
        $query = '';
    } else {
        $base = '';
        $query = $uri;
    }

    qc_parse_str( $query, $qs );
    $qs = urlencode_deep( $qs ); // this re-URL-encodes things that were already in the query string
    if ( is_array( $args[0] ) ) {
        foreach ( $args[0] as $k => $v ) {
            $qs[ $k ] = $v;
        }
    } else {
        $qs[ $args[0] ] = $args[1];
    }

    foreach ( $qs as $k => $v ) {
        if ( $v === false )
            unset( $qs[$k] );
    }

    $ret = build_query( $qs );
    $ret = trim( $ret, '?' );
    $ret = preg_replace( '#=(&|$)#', '$1', $ret );
    $ret = $protocol . $base . $ret . $frag;
    $ret = rtrim( $ret, '?' );
    return $ret;
}
/**
 * Build URL query based on an associative and, or indexed array.
 *
 * This is a convenient function for easily building url queries. It sets the
 * separator to '&' and uses _http_build_query() function.
 *
 * @since 2.3.0
 *
 * @see _http_build_query() Used to build the query
 * @link https://secure.php.net/manual/en/function.http-build-query.php for more on what
 *		 http_build_query() does.
 *
 * @param array $data URL-encode key/value pairs.
 * @return string URL-encoded string.
 */
function build_query( $data ) {
    return _http_build_query( $data, null, '&', '', false );
}

/**
 * From php.net (modified by Mark Jaquith to behave like the native PHP5 function).
 *
 * @since 3.2.0
 * @access private
 *
 * @see https://secure.php.net/manual/en/function.http-build-query.php
 *
 * @param array|object  $data       An array or object of data. Converted to array.
 * @param string        $prefix     Optional. Numeric index. If set, start parameter numbering with it.
 *                                  Default null.
 * @param string        $sep        Optional. Argument separator; defaults to 'arg_separator.output'.
 *                                  Default null.
 * @param string        $key        Optional. Used to prefix key name. Default empty.
 * @param bool          $urlencode  Optional. Whether to use urlencode() in the result. Default true.
 *
 * @return string The query string.
 */
function _http_build_query( $data, $prefix = null, $sep = null, $key = '', $urlencode = true ) {
    $ret = array();

    foreach ( (array) $data as $k => $v ) {
        if ( $urlencode)
            $k = urlencode($k);
        if ( is_int($k) && $prefix != null )
            $k = $prefix.$k;
        if ( !empty($key) )
            $k = $key . '%5B' . $k . '%5D';
        if ( $v === null )
            continue;
        elseif ( $v === false )
            $v = '0';

        if ( is_array($v) || is_object($v) )
            array_push($ret,_http_build_query($v, '', $sep, $k, $urlencode));
        elseif ( $urlencode )
            array_push($ret, $k.'='.urlencode($v));
        else
            array_push($ret, $k.'='.$v);
    }

    if ( null === $sep )
        $sep = ini_get('arg_separator.output');

    return implode($sep, $ret);
}
function qc_parse_url( $url, $component = -1 ) {
	$to_unset = array();
	$url = strval( $url );

	if ( '//' === substr( $url, 0, 2 ) ) {
		$to_unset[] = 'scheme';
		$url = 'placeholder:' . $url;
	} elseif ( '/' === substr( $url, 0, 1 ) ) {
		$to_unset[] = 'scheme';
		$to_unset[] = 'host';
		$url = 'placeholder://placeholder' . $url;
	}

	$parts = @parse_url( $url );

	if ( false === $parts ) {
		// Parsing failure.
		return $parts;
	}

	// Remove the placeholder values.
	foreach ( $to_unset as $key ) {
		unset( $parts[ $key ] );
	}

	return $parts;
}
function get_redirect_final_target($url)
{
    $client=new \GuzzleHttp\Client();
    $lastUrl=$url;
    try {
        $client->head($url, [
            'on_stats' => function (\GuzzleHttp\TransferStats $stats) use (&$lastUrl) {
                $lastUrl = $stats->getEffectiveUri();
            }
        ]);
    }catch (\GuzzleHttp\Exception\TransferException $e){

    }
    return $lastUrl;
}
if(!function_exists('http_parse_cookie')) {
    function http_parse_cookie($string)
    {
        $results=[];
        $parts=array_filter(explode('; ',$string));
        foreach ($parts as $part){
            $pair=explode('=',$part);
            if(count($pair)>=2){
                $results[$pair[0]]=urldecode($pair[1]);
            }
        }
        return $results;
    }
}