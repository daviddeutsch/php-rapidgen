<?php

namespace PHPRapidGen;

class Facade
{
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
