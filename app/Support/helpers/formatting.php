<?php
function zeroise( $number, $threshold ) {
	return sprintf( '%0' . $threshold . 's', $number );
}

function esc_html($text){
	$safe_text=la_check_invalid_utf8($text);
    return htmlspecialchars($safe_text,ENT_QUOTES);
}
function esc_attr( $text ) {
	$safe_text=la_check_invalid_utf8($text);
	$safe_text = htmlspecialchars( $safe_text, ENT_QUOTES );
	return $safe_text;
}
function la_check_invalid_utf8($string, $strip = false ) {
	$string = (string) $string;

	if ( 0 === strlen( $string ) ) {
		return '';
	}

	// Store the site charset as a static to avoid multiple calls to get_option()
	static $is_utf8 = null;
	if ( ! isset( $is_utf8 ) ) {
		$is_utf8 = true;
	}
	if ( ! $is_utf8 ) {
		return $string;
	}

	// Check for support for utf8 in the installed PCRE library once and store the result in a static
	static $utf8_pcre = null;
	if ( ! isset( $utf8_pcre ) ) {
		$utf8_pcre = @preg_match( '/^./u', 'a' );
	}
	// We can't demand utf8 in the PCRE installation, so just return the string in those cases
	if ( !$utf8_pcre ) {
		return $string;
	}

	// preg_match fails when it encounters invalid UTF8 in $string
	if ( 1 === @preg_match( '/^./us', $string ) ) {
		return $string;
	}

	// Attempt to strip the bad chars if requested (not recommended)
	if ( $strip && function_exists( 'iconv' ) ) {
		return iconv( 'utf-8', 'utf-8', $string );
	}

	return '';
}



/**
 * Checks and cleans a URL.
 *
 * A number of characters are removed from the URL. If the URL is for displaying
 * (the default behaviour) ampersands are also replaced. The {@see 'clean_url'} filter
 * is applied to the returned cleaned URL.
 *
 * @since 2.8.0
 *
 * @param string $url       The URL to be cleaned.
 * @param array  $protocols Optional. An array of acceptable protocols.
 *		                    Defaults to return value of wp_allowed_protocols()
 * @param string $_context  Private. Use esc_url_raw() for database usage.
 * @return string The cleaned $url after the {@see 'clean_url'} filter is applied.
 */
function esc_url( $url, $protocols = null, $_context = 'display' ) {

	if ( '' == $url )
		return $url;

	$url = str_replace( ' ', '%20', $url );
	$url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\[\]\\x80-\\xff]|i', '', $url);

	if ( '' === $url ) {
		return $url;
	}

	if ( 0 !== stripos( $url, 'mailto:' ) ) {
		$strip = array('%0d', '%0a', '%0D', '%0A');
		$url = _deep_replace($strip, $url);
	}

	$url = str_replace(';//', '://', $url);
	/* If the URL doesn't appear to contain a scheme, we
	 * presume it needs http:// prepended (unless a relative
	 * link starting with /, # or ? or a php file).
	 */
	if ( strpos($url, ':') === false && ! in_array( $url[0], array( '/', '#', '?' ) ) &&
	     ! preg_match('/^[a-z0-9-]+?\.php/i', $url) )
		$url = 'http://' . $url;

	// Replace ampersands and single quotes only when displaying.
	if ( 'display' == $_context ) {
		$url = str_replace( '&amp;', '&#038;', $url );
		$url = str_replace( "'", '&#039;', $url );
	}

	if ( ( false !== strpos( $url, '[' ) ) || ( false !== strpos( $url, ']' ) ) ) {

		$parsed = qc_parse_url( $url );
		$front  = '';

		if ( isset( $parsed['scheme'] ) ) {
			$front .= $parsed['scheme'] . '://';
		} elseif ( '/' === $url[0] ) {
			$front .= '//';
		}

		if ( isset( $parsed['user'] ) ) {
			$front .= $parsed['user'];
		}

		if ( isset( $parsed['pass'] ) ) {
			$front .= ':' . $parsed['pass'];
		}

		if ( isset( $parsed['user'] ) || isset( $parsed['pass'] ) ) {
			$front .= '@';
		}

		if ( isset( $parsed['host'] ) ) {
			$front .= $parsed['host'];
		}

		if ( isset( $parsed['port'] ) ) {
			$front .= ':' . $parsed['port'];
		}

		$end_dirty = str_replace( $front, '', $url );
		$end_clean = str_replace( array( '[', ']' ), array( '%5B', '%5D' ), $end_dirty );
		$url       = str_replace( $end_dirty, $end_clean, $url );

	}
	return $url;
}

function autolink($str, $attributes=array()) {
    $attrs = '';
    foreach ($attributes as $attribute => $value) {
        $attrs .= " {$attribute}=\"{$value}\"";
    }

    $str = ' ' . $str;
    $str = preg_replace(
        '`([^"=\'>])((http|https|ftp)://[^\s<]+[^\s<\.)])`i',
        '$1<a href="$2"'.$attrs.'>$2</a>',
        $str
    );
    $str = substr($str, 1);

    return $str;
}

function _split_str_by_whitespace( $string, $goal ) {
    $chunks = array();

    $string_nullspace = strtr( $string, "\r\n\t\v\f ", "\000\000\000\000\000\000" );

    while ( $goal < strlen( $string_nullspace ) ) {
        $pos = strrpos( substr( $string_nullspace, 0, $goal + 1 ), "\000" );

        if ( false === $pos ) {
            $pos = strpos( $string_nullspace, "\000", $goal + 1 );
            if ( false === $pos ) {
                break;
            }
        }

        $chunks[] = substr( $string, 0, $pos + 1 );
        $string = substr( $string, $pos + 1 );
        $string_nullspace = substr( $string_nullspace, $pos + 1 );
    }

    if ( $string ) {
        $chunks[] = $string;
    }

    return $chunks;
}

/**
 * Convert plaintext URI to HTML links.
 *
 * Converts URI, www and ftp, and email addresses. Finishes by fixing links
 * within links.
 *
 * @since 0.71
 *
 * @param string $text Content to convert URIs.
 * @return string Content with converted URIs.
 */
function make_clickable( $text,$attributes=[] ) {
    $r = '';
    $textarr = preg_split( '/(<[^<>]+>)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE ); // split out HTML tags
    $nested_code_pre = 0; // Keep track of how many levels link is nested inside <pre> or <code>
    foreach ( $textarr as $piece ) {

        if ( preg_match( '|^<code[\s>]|i', $piece ) || preg_match( '|^<pre[\s>]|i', $piece ) || preg_match( '|^<script[\s>]|i', $piece ) || preg_match( '|^<style[\s>]|i', $piece ) )
            $nested_code_pre++;
        elseif ( $nested_code_pre && ( '</code>' === strtolower( $piece ) || '</pre>' === strtolower( $piece ) || '</script>' === strtolower( $piece ) || '</style>' === strtolower( $piece ) ) )
            $nested_code_pre--;

        if ( $nested_code_pre || empty( $piece ) || ( $piece[0] === '<' && ! preg_match( '|^<\s*[\w]{1,20}+://|', $piece ) ) ) {
            $r .= $piece;
            continue;
        }

        // Long strings might contain expensive edge cases ...
        if ( 10000 < strlen( $piece ) ) {
            // ... break it up
            foreach ( _split_str_by_whitespace( $piece, 2100 ) as $chunk ) { // 2100: Extra room for scheme and leading and trailing paretheses
                if ( 2101 < strlen( $chunk ) ) {
                    $r .= $chunk; // Too big, no whitespace: bail.
                } else {
                    $r .= make_clickable( $chunk );
                }
            }
        } else {
            $ret = " $piece "; // Pad with whitespace to simplify the regexes

            $url_clickable = '~
				([\\s(<.,;:!?])                                        # 1: Leading whitespace, or punctuation
				(                                                      # 2: URL
					[\\w]{1,20}+://                                # Scheme and hier-part prefix
					(?=\S{1,2000}\s)                               # Limit to URLs less than about 2000 characters long
					[\\w\\x80-\\xff#%\\~/@\\[\\]*(+=&$-]*+         # Non-punctuation URL character
					(?:                                            # Unroll the Loop: Only allow puctuation URL character if followed by a non-punctuation URL character
						[\'.,;:!?)]                            # Punctuation URL character
						[\\w\\x80-\\xff#%\\~/@\\[\\]*(+=&$-]++ # Non-punctuation URL character
					)*
				)
				(\)?)                                                  # 3: Trailing closing parenthesis (for parethesis balancing post processing)
			~xS'; // The regex is a non-anchored pattern and does not have a single fixed starting character.
            // Tell PCRE to spend more time optimizing since, when used on a page load, it will probably be used several times.

            $ret = preg_replace_callback( $url_clickable, '_make_url_clickable_cb', $ret );

            //$ret = preg_replace_callback( '#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]+)#is', '_make_web_ftp_clickable_cb', $ret );
            //$ret = preg_replace_callback( '#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', '_make_email_clickable_cb', $ret );

            $ret = substr( $ret, 1, -1 ); // Remove our whitespace padding.
            $r .= $ret;
        }
    }

    // Cleanup of accidental links within links
    return preg_replace( '#(<a([ \r\n\t]+[^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i', "$1$3</a>", $r );
}

/**
 * Callback to convert URI match to HTML A element.
 *
 * This function was backported from 2.5.0 to 2.3.2. Regex callback for make_clickable().
 *
 * @since 2.3.2
 * @access private
 *
 * @param array $matches Single Regex Match.
 * @return string HTML A element with URI address.
 */
function _make_url_clickable_cb( $matches ) {
    $url = $matches[2];

    if ( ')' == $matches[3] && strpos( $url, '(' ) ) {
        // If the trailing character is a closing parethesis, and the URL has an opening parenthesis in it, add the closing parenthesis to the URL.
        // Then we can let the parenthesis balancer do its thing below.
        $url .= $matches[3];
        $suffix = '';
    } else {
        $suffix = $matches[3];
    }

    // Include parentheses in the URL only if paired
    while ( substr_count( $url, '(' ) < substr_count( $url, ')' ) ) {
        $suffix = strrchr( $url, ')' ) . $suffix;
        $url = substr( $url, 0, strrpos( $url, ')' ) );
    }

    $url = esc_url($url);
    if ( empty($url) )
        return $matches[0];

    return $matches[1] . "<a href=\"$url\" rel=\"nofollow noopener noreferrer\">$url</a>" . $suffix;
}
/**
 * Callback to convert URL match to HTML A element.
 *
 * This function was backported from 2.5.0 to 2.3.2. Regex callback for make_clickable().
 *
 * @since 2.3.2
 * @access private
 *
 * @param array $matches Single Regex Match.
 * @return string HTML A element with URL address.
 */
function _make_web_ftp_clickable_cb( $matches ) {
    $ret = '';
    $dest = $matches[2];
    $dest = 'http://' . $dest;

    // removed trailing [.,;:)] from URL
    if ( in_array( substr($dest, -1), array('.', ',', ';', ':', ')') ) === true ) {
        $ret = substr($dest, -1);
        $dest = substr($dest, 0, strlen($dest)-1);
    }

    $dest = esc_url($dest);
    if ( empty($dest) )
        return $matches[0];

    return $matches[1] . "<a href=\"$dest\" rel=\"nofollow\">$dest</a>$ret";
}