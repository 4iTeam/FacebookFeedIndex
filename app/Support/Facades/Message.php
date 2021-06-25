<?php
namespace App\Support\Facades;

use Illuminate\Support\Facades\Session;

class Message {

	public static function rawSuccess( $message ) {
		static::rawMessage( $message, 'alert-success' );
	}

	public static function rawInfo( $message ) {
		static::rawMessage( $message, 'alert-info' );
	}

	public static function rawWarning( $message ) {
		static::rawMessage( $message, 'alert-warning' );
	}

	public static function rawDanger( $message ) {
		static::rawMessage( $message, 'alert-danger' );
	}

	public static function rawError( $message ) {
		static::rawDanger( $message );
	}

	public static function success( $message ) {
		static::message( $message, 'alert-success' );
	}

	public static function info( $message ) {
		static::message( $message, 'alert-info' );
	}

	public static function warning( $message ) {
		static::message( $message, 'alert-warning' );
	}

	public static function danger( $message ) {
		static::message( $message, 'alert-danger' );
	}

	public static function error( $message ) {
		static::danger( $message );
	}

	public static function rawMessage( $message, $class ) {
		static::message( $message, $class );
		static::setRaw();
	}

	public static function withTemplate( $template ) {
		Session::flash( 'message-template', $template );
	}

	public static function message( $message, $class ) {
		Session::flash( 'message', $message );
		Session::flash( 'message-class', $class );
	}

	public static function setRaw( $flag = true ) {
		Session::flash( 'message-raw', $flag );
	}

	public static function has() {
		return Session::has( 'message' ) && Session::get( 'message' );
	}

	public static function display( $dismissable = false ) {
		$class = 'alert';
		if ( $dismissable ) {
			$class .= ' alert-dismissable';
		}
		$class .= ' ' . Session::get( 'message-class', 'alert-success' );
		$isRaw    = Session::get( 'message-raw' );
		$template = Session::get( 'message-template' );
		if ( static::has() ) {
			$message = '';
			if ( $dismissable ) {
				$message = '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
			}
			if ( $isRaw ) {
				$message .= Session::get( 'message' );
			} else {
				$message .= htmlentities( Session::get( 'message' ) );
			}

			if ( $template ) {
				$message .= view( $template );
			}
			printf( '<div class="%s">%s</div>', $class, $message );
		}
	}
}