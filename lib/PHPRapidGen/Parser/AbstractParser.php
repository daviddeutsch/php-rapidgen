<?php

namespace PHPRapidGen\Parser;

abstract class AbstractParser
{
	public static $template;
	public static $helper;

	private static $options = [];

	public function __construct( $options=[] )
	{
		if ( !empty( $options ) ) {
			foreach ( $options as $k => $v ) {
				self::$options[$k] = $v;
			}
		}

		self::$template = new self::$options['template_class']();
		self::$helper   = new self::$options['helper_class']();
	}

	public function parse( $nodeset )
	{
		if ( is_array($nodeset) ) {
			$array = [];
			foreach ( $nodeset as $node ) {
				$array[] = $this->parse($node);
			}

			return $array;
		}

		if ( !is_object($nodeset) ) {
			return $nodeset;
		}

		return $this->node( $nodeset );
	}

	private function node( $input )
	{
		return (string) $input;
	}
}
