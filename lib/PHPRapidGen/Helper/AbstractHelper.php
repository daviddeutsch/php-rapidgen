<?php

namespace PHPRapidGen\Helper;

class AbstractHelper
{
	public static function __call( $name, $args )
	{
		if ( method_exists( __CLASS__, $name ) ) {
			return call_user_func( array( 'self', $name ), $args[0] );
		} elseif ( method_exists( __CLASS__, '_'.$name ) ) {
			return call_user_func( array( 'self', '_'.$name ), $args[0] );
		}

		return '';
	}
}
