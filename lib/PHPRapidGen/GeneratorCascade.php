<?php

namespace PHPRapidGen;

class GeneratorCascade
{
	private static $cascade = [];

	private static $pointer = 0;

	public function &current()
	{
		if ( empty(self::$cascade[0]) ) {
			self::$cascade[] = new PHPRapidGen;
		}

		return self::$cascade[self::$pointer];
	}

	public function child( $context )
	{
		self::$cascade[] = clone self::current();
	}
}
