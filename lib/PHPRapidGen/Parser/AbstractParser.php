<?php

namespace PHPRapidGen\Parser;

use PHPRapidGen\Facade as RapidGenerator;
use PHPRapidGen\Helper\Basic as BasicHelper;

abstract class AbstractParser
{
	/**
	 * @var callable
	 */
	public $template;

	/**
	 * @var \PHPRapidGen\Helper\Basic
	 */
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

		$this->helper = new BasicHelper;

		$this->template = function( $template, $context ) {
			return RapidGenerator::parseChild($template, $context);
		};
	}

	public function context( $context )
	{
		if ( is_string($context) ) {
			$this->context = $this->resolveContext($context);
		} else {
			$this->context = $context;
		}
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
