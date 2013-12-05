<?php

namespace PHPRapidGen\Parser;

abstract class AbstractParser
{
	public $template;
	public $helper;
	public $context;

	private $source;

	private static $options = [];

	public function __construct( $options=[] )
	{
		if ( !empty( $options ) ) {
			foreach ( $options as $k => $v ) {
				self::$options[$k] = $v;
			}
		}

		$this->template = new self::$options['template_class']();
		$this->helper   = new self::$options['helper_class']();
	}

	public function context( $context )
	{
		$this->context = $context;
	}

	public function parse( $source )
	{
		$this->source = $source;
	}

	protected function node( $input )
	{
		return (string) $input;
	}
}
