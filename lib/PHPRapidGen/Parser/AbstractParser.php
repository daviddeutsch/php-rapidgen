<?php

namespace PHPRapidGen\Parser;

//use PHPRapidGen\Helper\Template as TemplateHelper;
use PHPRapidGen\Helper\Basic as BasicHelper;

abstract class AbstractParser
{
	public $template;
	public $helper;
	public $context;
	public $context_partial;

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
		$this->helper = new BasicHelper;
	}

	public function context( $context )
	{
		$this->context = $context;
	}

	public function contextPartial( $key )
	{
		$this->$context_partial = $key;
	}

	public function parse( $source )
	{
		$this->source = $source;
	}

	protected function node( $input )
	{
		return (string) $input;
	}

	protected function resolveContext( $key )
	{
		if ( !is_array( $key ) ) {
			$key = explode( '.', $key );
		}

		if ( empty( $key ) ) return false;

		$return = $this->context;

		foreach ( $key as $k ) {
			if ( is_object( $return ) ) {
				if ( property_exists( $return, $k ) ) {
					$return = $return->$k;
				}
			} elseif ( is_array( $return ) ) {
				if ( isset( $return[$k] ) ) {
					$return = $return[$k];
				}
			}
		}

		return $return;
	}
}
