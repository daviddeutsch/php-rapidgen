<?php

namespace PHPRapidGen;

use PHPRapidGen\Parser\PHPParser;
use PHPRapidGen\Parser\SlimPHPParser;
use PHPRapidGen\Parser\HandlebarsParser;

class Facade
{
	/**
	 * @var array
	 */
	private static $types = [
		'php'        => 'PHPParser',
		'json'       => 'SlimPHPParser',
		'handlebars' => 'HandlebarsParser'
	];

	/**
	 * @var array|Parser\AbstractParser
	 */
	private static $parsers = [];

	static function configure()
	{
		foreach ( self::$types as $extension => $class ) {
			self::$parsers[$extension] = new $class();
		}
	}

	static function context( $context )
	{
		foreach ( self::$parsers as $parser ) {
			$parser->context($context);
		}
	}

	static function batch( $array )
	{
		foreach ( $array as $target => $source ) {
			self::convert($source, $target);
		}
	}

	static function convert( $source, $target )
	{
		$extension = pathinfo($source, PATHINFO_EXTENSION);

		if ( !isset(self::$parsers[$extension]) ) {
			return null;
		}

		self::$parsers[$extension]->parse($source);
	}
}
