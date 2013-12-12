<?php

namespace PHPRapidGen;

class GeneratorCascade
{
	/**
	 * @var array List of successive generators
	 */
	private static $cascade = [];

	/**
	 * @var int Pointer to current generator
	 */
	private static $pointer = 0;

	public static function &start( $context=null )
	{
		if ( !empty(self::$cascade) ) {
			self::$cascade = [];
		}

		self::$cascade[] = new PHPRapidGen($context);

		return self::current();
	}

	/**
	 * @return PHPRapidGen
	 */
	public static function &current()
	{
		if ( empty(self::$cascade[0]) ) {
			self::$cascade[] = new PHPRapidGen;
		}

		return self::$cascade[self::$pointer];
	}

	/**
	 * @param $context mixed Full context, or dot path to a context child item
	 *
	 * @return PHPRapidGen
	 */
	public static function &child( $context )
	{
		self::$cascade[] = clone self::current();

		self::$pointer++;

		return self::current()->context($context);
	}

	/**
	 * Step out of and remove child generator
	 *
	 * @return PHPRapidGen
	 */
	public static function &parent()
	{
		array_pop(self::$cascade);

		self::$pointer--;

		return self::current();
	}
}
