<?php

namespace PHPRapidGen;

class Facade
{
	private static $parsers;

	static function configure()
	{
		self::$parsers = (object) [
			'phpp' => new Parser\PHPParser(),
			'sphpp' => new Parser\SlimPHPParser(),
			'hb' => new Parser\Handlebars()
		];
	}

	static function context( $context )
	{

	}

	static function generate( $source, $target )
	{
		$class = 'Parser\\';

		if ( file_exists($source.'/root.php') ) {
			$class .= 'PHPParser';
		} elseif ( file_exists($source.'/root.json') ) {
			$class .= 'SlimPHPParser';
		} elseif ( file_exists($source.'/root.handlebars') ) {
			$class .= 'Handlebars';
		}

		$parser = new $class;

		$parser->parse();
	}

	static function parser( $type, $source )
	{

	}
}
