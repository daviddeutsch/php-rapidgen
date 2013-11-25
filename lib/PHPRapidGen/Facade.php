<?php

namespace PHPRapidGen;

class Facade
{
	private static $parsers;

	static function configure()
	{
		self::$parsers = (object) [
			'php' => new Parser\PHPParser(),
			'json' => new Parser\SlimPHPParser(),
			'handlebars' => new Parser\Handlebars()
		];
	}

	static function context( $context )
	{
		foreach ( self::$parsers as $handle => $parser ) {
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

	}

	static function generate( $source, $target )
	{
		$extension = pathinfo($source, PATHINFO_EXTENSION);

		if ( !isset(self::$parsers[$extension]) ) {
			return null;
		}

		self::$parsers[$extension]->parse( $source );
	}

	static function parser( $type, $source )
	{

	}
}
