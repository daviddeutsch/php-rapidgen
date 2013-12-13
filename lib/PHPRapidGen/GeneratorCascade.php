<?php

namespace PHPRapidGen;

class GeneratorCascade extends \ArrayIterator
{
	/**
	 * Creates a new cascade
	 *
	 * @param mixed $context Context object, array or null
	 */
	public function __construct( $context=null )
	{
		return parent::__construct( array(new PHPRapidGen($context)) );
	}

	/**
	 * Generate a new Generator entry that inherits from the previous one
	 *
	 * @param mixed $context New context, or dot path to child in last context
	 *
	 * @return PHPRapidGen
	 */
	public function append( $context=null )
	{
		parent::append( clone parent::current() );

		if ( !empty($context) ) {
			parent::current()->context($context);
		}
	}

	public function batch( $array, $context=null )
	{
		foreach ( $array as $target => $source ) {
			self::convert($target, $source, $context);
		}
	}

	function convert( $target, $source, $context=null )
	{
		if ( is_array($source) ) {
			$this->append($source[1]);

			$source = $source[0];
		} else {
			$this->append($context);
		}

		parent::current()->convert($source, $target);

		self::pop();
	}

	/**
	 * Shorten cascade by the last item
	 *
	 * @return PHPRapidGen
	 */
	public function pop()
	{
		return array_pop($this);
	}

	public function parse( $source, $extension=null )
	{
		if ( empty($extension) ) {

		}

		return self::$parsers[$extension]->parse($source);
	}
}
