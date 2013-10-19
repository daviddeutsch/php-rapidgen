<?php

namespace PHPRapidGen\Parser;

abstract class AbstractParser
{
	public static $template;
	public static $helper;

	public static $options = [];

	public static function configure( $options=[] )
	{
		$opts = self::$options;

		if ( !empty( $options ) ) {
			foreach ( $options as $k => $v ) {
				$opts[$k] = $v;
			}
		}

		self::$template = new $opts['template_class'];
		self::$helper   = new $opts['helper_class'];
	}

	public static function parse( $nodeset )
	{
		if ( is_array($nodeset) ) {
			$array = [];
			foreach ( $nodeset as $node ) {
				$array[] = self::parse($node);
			}

			return $array;
		}

		if ( !is_object($nodeset) ) {
			return $nodeset;
		}

		return self::node( $nodeset );
	}

	public static function node( $input )
	{
		return (string) $input;
	}
}
