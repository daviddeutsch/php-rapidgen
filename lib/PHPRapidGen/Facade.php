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
		'php'        => 'Parser\PHPParser',
		'json'       => 'Parser\SlimPHPParser',
		'handlebars' => 'Parser\HandlebarsParser'
	];

	/**
	 * @var array|Parser\AbstractParser
	 */
	private static $parsers = [];

	static function configure()
	{
		self::$parsers['json'] = new Parser\SlimPHPParser;
		self::$parsers['handlebars'] = new Parser\HandlebarsParser;
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
