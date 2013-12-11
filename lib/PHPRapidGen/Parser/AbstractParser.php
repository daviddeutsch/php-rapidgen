<?php

namespace PHPRapidGen\Parser;

//use PHPRapidGen\Helper\Template as TemplateHelper;
use PHPRapidGen\Helper\Basic as BasicHelper;

abstract class AbstractParser
{
	public $template;
	public $helper;
	public $context;

	private $source;

	private static $options = [
		'helper_class' => '',
		'template_class' => 'Helper\Template',
	];

	public function __construct( $options=[] )
	{
		if ( !empty($options) ) {
			foreach ( $options as $k => $v ) {
				self::$options[$k] = $v;
			}
		}

		//$this->template = new TemplateHelper;
		$this->helper   = new BasicHelper;
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
