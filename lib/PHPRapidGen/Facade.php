<?php

namespace PHPRapidGen;

class Facade
{
	/**
	 * @var GeneratorCascade;
	 */
	private static $cascade;

	/**
	 * Batch process a list of targets and sources
	 *
	 * @param array $array    ['target_path' => 'source_path'];
	 *                        The value of each target can also be an array:
	 *                        ['target' => ['source', 'context'];
	 *                        then, the second parameter is a new context or
	 *                        dot path to context child item
	 * @param mixed $context Root context object or array
	 */
	static function batch( $array, $context )
	{
		if ( empty(self::$cascade) ) {
			self::$cascade = new GeneratorCascade($context);
		}

		self::$cascade->batch($array);
	}

	/**
	 * Convert one target from one source
	 *
	 * @param mixed  $target  Path to desired target file
	 * @param string $source  Path to source file
	 * @param mixed  $context Context object or array, or dot notation path
	 *                        to child context
	 *
	 * @return mixed
	 */
	static function convert( $target, $source, $context=null )
	{
		self::$cascade->convert($target, $source, $context);
	}

	static function helpers( $helpers )
	{
		// TODO;
	}
}
