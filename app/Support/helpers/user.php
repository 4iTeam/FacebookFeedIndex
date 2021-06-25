<?php
/**
 * @param $capability
 *
 * @return bool
 */
function current_user_can( $capability ) {
	$current_user = Auth::user();
	/**
	 * @var \App\User $current_user
	 */
	if ( empty( $current_user ) ) {
		return false;
	}
	$args = array_slice( func_get_args(), 1 );
	$args = array_merge( array( $capability ), $args );

	return call_user_func_array( array( $current_user, 'can' ), $args );
}

function current_user_is( $role ) {
	$current_user = Auth::user();
	/**
	 * @var \App\User $current_user
	 */
	if ( empty( $current_user ) ) {
		return false;
	}

	return $current_user->has_cap( $role );
}

function current_user_id() {
	return get_current_user_id();
}

function get_current_user_id() {
	$current_user = Auth::user();
	/**
	 * @var \App\User $current_user
	 */
	if ( empty( $current_user ) ) {
		return false;
	}

	return $current_user->id;
}

/**
 * @return App\User | null
 */
function current_user() {
	return Auth::user();
}

/**
 * @param $key
 *
 * @return mixed|null
 */
function user_meta( $key ) {
	$args = func_get_args();
	if ( $user = current_user() ) {
		return $user->meta( ...$args );
	}

	return null;
}

function generate_random_password( $length = 12, $special_chars = true, $extra_special_chars = false ) {

	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	if ( $special_chars ) {
		$chars .= '!@#$%^&*()';
	}
	if ( $extra_special_chars ) {
		$chars .= '-_ []{}<>~`+=,.;:/?|';
	}

	$password = '';
	for ( $i = 0; $i < $length; $i ++ ) {
		$password .= substr( $chars, mt_rand( 0, strlen( $chars ) - 1 ), 1 );
	}

	/**
	 * Filters the randomly-generated password.
	 *
	 * @since 3.0.0
	 *
	 * @param string $password The generated password.
	 */
	return $password;
}



/**
 * Retrieves the avatar URL.
 *
 * @since 4.2.0
 *
 * @param mixed $id_or_email The Gravatar to retrieve a URL for. Accepts a user_id, gravatar md5 hash,
 *                           user email, WP_User object, WP_Post object, or WP_Comment object.
 * @param array $args {
 *     Optional. Arguments to return instead of the default arguments.
 *
 *     @type int    $size           Height and width of the avatar in pixels. Default 96.
 *     @type string $default        URL for the default image or a default type. Accepts '404' (return
 *                                  a 404 instead of a default image), 'retro' (8bit), 'monsterid' (monster),
 *                                  'wavatar' (cartoon face), 'indenticon' (the "quilt"), 'mystery', 'mm',
 *                                  or 'mysteryman' (The Oyster Man), 'blank' (transparent GIF), or
 *                                  'gravatar_default' (the Gravatar logo). Default is the value of the
 *                                  'avatar_default' option, with a fallback of 'mystery'.
 *     @type bool   $force_default  Whether to always show the default image, never the Gravatar. Default false.
 *     @type string $rating         What rating to display avatars up to. Accepts 'G', 'PG', 'R', 'X', and are
 *                                  judged in that order. Default is the value of the 'avatar_rating' option.
 *     @type string $scheme         URL scheme to use. See set_url_scheme() for accepted values.
 *                                  Default null.
 *     @type array  $processed_args When the function returns, the value will be the processed/sanitized $args
 *                                  plus a "found_avatar" guess. Pass as a reference. Default null.
 * }
 * @return false|string The URL of the avatar we found, or false if we couldn't find an avatar.
 */
function get_gravatar_url( $id_or_email, $args = null ) {
    $args = get_gravatar_data( $id_or_email, $args );
    return $args['url'];
}

/**
 * Retrieves default data about the avatar.
 *
 * @since 4.2.0
 *
 * @param mixed $id_or_email The Gravatar to retrieve. Accepts a user_id, gravatar md5 hash,
 *                            user email, WP_User object, WP_Post object, or WP_Comment object.
 * @param array $args {
 *     Optional. Arguments to return instead of the default arguments.
 *
 *     @type int    $size           Height and width of the avatar image file in pixels. Default 96.
 *     @type int    $height         Display height of the avatar in pixels. Defaults to $size.
 *     @type int    $width          Display width of the avatar in pixels. Defaults to $size.
 *     @type string $default        URL for the default image or a default type. Accepts '404' (return
 *                                  a 404 instead of a default image), 'retro' (8bit), 'monsterid' (monster),
 *                                  'wavatar' (cartoon face), 'indenticon' (the "quilt"), 'mystery', 'mm',
 *                                  or 'mysteryman' (The Oyster Man), 'blank' (transparent GIF), or
 *                                  'gravatar_default' (the Gravatar logo). Default is the value of the
 *                                  'avatar_default' option, with a fallback of 'mystery'.
 *     @type bool   $force_default  Whether to always show the default image, never the Gravatar. Default false.
 *     @type string $rating         What rating to display avatars up to. Accepts 'G', 'PG', 'R', 'X', and are
 *                                  judged in that order. Default is the value of the 'avatar_rating' option.
 *     @type string $scheme         URL scheme to use. See set_url_scheme() for accepted values.
 *                                  Default null.
 *     @type array  $processed_args When the function returns, the value will be the processed/sanitized $args
 *                                  plus a "found_avatar" guess. Pass as a reference. Default null.
 *     @type string $extra_attr     HTML attributes to insert in the IMG element. Is not sanitized. Default empty.
 * }
 * @return array $processed_args {
 *     Along with the arguments passed in `$args`, this will contain a couple of extra arguments.
 *
 *     @type bool   $found_avatar True if we were able to find an avatar for this user,
 *                                false or not set if we couldn't.
 *     @type string $url          The URL of the avatar we found.
 * }
 */
function get_gravatar_data( $id_or_email, $args = null ) {
    $args = la_parse_args( $args, array(
        'size'           => 96,
        'height'         => null,
        'width'          => null,
        'default'        => get_option( 'avatar_default', 'mystery' ),
        'force_default'  => false,
        'rating'         => get_option( 'avatar_rating' ),
        'scheme'         => null,
        'processed_args' => null, // if used, should be a reference
        'extra_attr'     => '',
    ) );

    if ( is_numeric( $args['size'] ) ) {
        $args['size'] = absint( $args['size'] );
        if ( ! $args['size'] ) {
            $args['size'] = 96;
        }
    } else {
        $args['size'] = 96;
    }

    if ( is_numeric( $args['height'] ) ) {
        $args['height'] = absint( $args['height'] );
        if ( ! $args['height'] ) {
            $args['height'] = $args['size'];
        }
    } else {
        $args['height'] = $args['size'];
    }

    if ( is_numeric( $args['width'] ) ) {
        $args['width'] = absint( $args['width'] );
        if ( ! $args['width'] ) {
            $args['width'] = $args['size'];
        }
    } else {
        $args['width'] = $args['size'];
    }

    if ( empty( $args['default'] ) ) {
        $args['default'] = get_option( 'avatar_default', 'mystery' );
    }

    switch ( $args['default'] ) {
        case 'mm' :
        case 'mystery' :
        case 'mysteryman' :
            $args['default'] = 'mm';
            break;
        case 'gravatar_default' :
            $args['default'] = false;
            break;
    }

    $args['force_default'] = (bool) $args['force_default'];

    $args['rating'] = strtolower( $args['rating'] );

    $args['found_avatar'] = false;


    if ( isset( $args['url'] ) && ! is_null( $args['url'] ) ) {
        return $args;
    }

    $email_hash = '';
    $user = $email = false;

    // Process the user identifier.
    if ( is_numeric( $id_or_email ) ) {
        $user = \App\User::find($id_or_email);
    } elseif ( is_string( $id_or_email ) ) {
        if ( strpos( $id_or_email, '@md5.gravatar.com' ) ) {
            // md5 hash
            list( $email_hash ) = explode( '@', $id_or_email );
        } else {
            // email address
            $email = $id_or_email;
        }
    } elseif ( $id_or_email instanceof \App\User ) {
        // User Object
        $user = $id_or_email;
    }

    if ( ! $email_hash ) {
        if ( $user ) {
            $email = $user->email;
        }

        if ( $email ) {
            $email_hash = md5( strtolower( trim( $email ) ) );
        }
    }

    if ( $email_hash ) {
        $args['found_avatar'] = true;
        $gravatar_server = hexdec( $email_hash[0] ) % 3;
    } else {
        $gravatar_server = rand( 0, 2 );
    }

    $url_args = array(
        's' => $args['size'],
        'd' => $args['default'],
        'f' => $args['force_default'] ? 'y' : false,
        'r' => $args['rating'],
    );

    if ( is_ssl() ) {
        $url = 'https://secure.gravatar.com/avatar/' . $email_hash;
    } else {
        $url = sprintf( 'http://%d.gravatar.com/avatar/%s', $gravatar_server, $email_hash );
    }

    $url = add_query_arg(array_filter( $url_args ),$url);

    $args['url'] = $url;

    return $args;
}

/**
 * Retrieve the avatar `<img>` tag for a user, email address, MD5 hash, comment, or post.
 *
 * @since 2.5.0
 * @since 4.2.0 Optional `$args` parameter added.
 *
 * @param mixed $id_or_email The Gravatar to retrieve. Accepts a user_id, gravatar md5 hash,
 *                           user email, WP_User object, WP_Post object, or WP_Comment object.
 * @param int    $size       Optional. Height and width of the avatar image file in pixels. Default 96.
 * @param string $default    Optional. URL for the default image or a default type. Accepts '404'
 *                           (return a 404 instead of a default image), 'retro' (8bit), 'monsterid'
 *                           (monster), 'wavatar' (cartoon face), 'indenticon' (the "quilt"),
 *                           'mystery', 'mm', or 'mysteryman' (The Oyster Man), 'blank' (transparent GIF),
 *                           or 'gravatar_default' (the Gravatar logo). Default is the value of the
 *                           'avatar_default' option, with a fallback of 'mystery'.
 * @param string $alt        Optional. Alternative text to use in &lt;img&gt; tag. Default empty.
 * @param array  $args       {
 *     Optional. Extra arguments to retrieve the avatar.
 *
 *     @type int          $height        Display height of the avatar in pixels. Defaults to $size.
 *     @type int          $width         Display width of the avatar in pixels. Defaults to $size.
 *     @type bool         $force_default Whether to always show the default image, never the Gravatar. Default false.
 *     @type string       $rating        What rating to display avatars up to. Accepts 'G', 'PG', 'R', 'X', and are
 *                                       judged in that order. Default is the value of the 'avatar_rating' option.
 *     @type string       $scheme        URL scheme to use. See set_url_scheme() for accepted values.
 *                                       Default null.
 *     @type array|string $class         Array or string of additional classes to add to the &lt;img&gt; element.
 *                                       Default null.
 *     @type bool         $force_display Whether to always show the avatar - ignores the show_avatars option.
 *                                       Default false.
 *     @type string       $extra_attr    HTML attributes to insert in the IMG element. Is not sanitized. Default empty.
 * }
 * @return false|string `<img>` tag for the user's avatar. False on failure.
 */
function get_gravatar( $id_or_email, $args=[] ) {
    $defaults = array(
        // get_avatar_data() args.
        'size'          => 96,
        'height'        => null,
        'width'         => null,
        'default'       => get_option( 'avatar_default', 'mystery' ),
        'force_default' => false,
        'rating'        => get_option( 'avatar_rating' ),
        'scheme'        => null,
        'alt'           => '',
        'class'         => null,
        'force_display' => false,
        'extra_attr'    => '',
    );

    if ( empty( $args ) ) {
        $args = array();
    }


    $args = la_parse_args( $args, $defaults );

    if ( empty( $args['height'] ) ) {
        $args['height'] = $args['size'];
    }
    if ( empty( $args['width'] ) ) {
        $args['width'] = $args['size'];
    }

    if ( ! $args['force_display'] && ! get_option( 'show_avatars' ) ) {
        //return false;
    }

    $url2x = get_gravatar_url( $id_or_email, array_merge( $args, array( 'size' => $args['size'] * 2 ) ) );

    $args = get_gravatar_data( $id_or_email, $args );

    $url = $args['url'];

    if ( ! $url ) {
        return false;
    }

    $class = array( 'avatar', 'avatar-' . (int) $args['size'], 'photo' );

    if ( ! $args['found_avatar'] || $args['force_default'] ) {
        $class[] = 'avatar-default';
    }

    if ( $args['class'] ) {
        if ( is_array( $args['class'] ) ) {
            $class = array_merge( $class, $args['class'] );
        } else {
            $class[] = $args['class'];
        }
    }

    $avatar = sprintf(
        "<img alt='%s' src='%s' srcset='%s' class='%s' height='%d' width='%d' %s/>",
        esc_attr( $args['alt'] ),
        esc_url( $url ),
        esc_url( $url2x ) . ' 2x',
        esc_attr( join( ' ', $class ) ),
        (int) $args['height'],
        (int) $args['width'],
        $args['extra_attr']
    );
    return $avatar;
}