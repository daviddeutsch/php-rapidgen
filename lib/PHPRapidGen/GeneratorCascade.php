<?php

namespace PHPRapidGen;

class GeneratorCascade extends \ArrayIterator
{
	public function __construct( $context=null )
	{
		parent::__construct( array(new PHPRapidGen($context)) );
	}

	/**
	 * Generate a new Generator entry that inherits from the previous one
	 *
	 * @param $context mixed New context, or dot path to child in last context
	 *
	 * @return PHPRapidGen
	 */
	public function append( $context=null )
	{
		parent::append( clone parent::current() );

		if ( $context ) {
			return parent::current()->context($context);
		} else {
			return parent::current();
		}
	}

	/**
	 * Step out of and remove child generator
	 *
	 * @return PHPRapidGen
	 */
	public function pop()
	{
		array_pop($this);

		return self::current();
	}
}
