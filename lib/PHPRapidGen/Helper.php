<?php

namespace PHPRapidGen;

class Helper
{
	public static function __callStatic( $name, $args )
	{
		if ( method_exists( __CLASS__, $name ) ) {
			call_user_func( array( 'self', $name ), $args[0] );
		} elseif ( method_exists( __CLASS__, '_'.$name ) ) {
			call_user_func( array( 'self', '_'.$name ), $args[0] );
		}
	}

	private static function _array( $input )
	{
		if (is_object($input)) {
			$input = get_object_vars($input);
		}

		if (!is_array($input) && !($input instanceof \Traversable)) {
			return '';
		}

		$is_assoc = $input !== array_values($input);

		$array = [];
		foreach ( $input as $k => $v ) {
			if ( is_array($v) ) {
				$content = self::_array($v);
			} elseif ( $is_assoc ) {
				$content = '{"s.String":"'.$v.'"},{"s.String":"'.$k.'"}';
			} else {
				$content = '{"s.String":"'.$v.'"}';
			}

			$array[] = '{"ArrayItem":['.$content.']}';
		}

		return '{"Array":[['.implode(',',$array)."]]}";
	}

	private static function docbloc( $input )
	{
		if (!is_array($input) && !($input instanceof \Traversable)) {
			return '';
		}

		$lines = [];
		foreach ( $input as $k => $v ) {
			$lines[] = ' * @'.$k.' '.$v;
		}

		return '{"Comment_Doc":["'."/**\n".implode("\n",$lines)."\n */".'"]}';
	}

	private static function concat( $input )
	{
		return implode($input);
	}
}
