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

	/**
	 * @var mixed
	 */
	public $context;

	private $source;

	private static $options = [
		'helper_class' => ''
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

	/**
	 * Assign a context to this parser, or select a sub-context from the current
	 * context
	 *
	 * @param mixed $context New context or a dot notation path to child context
	 */
	public function context( $context )
	{
		if ( is_string($context) ) {
			$this->context = $this->resolveContext($context);
		} else {
			$this->context = $context;
		}
	}

	/**
	 * @param $source
	 */
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
