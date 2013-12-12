<?php

namespace PHPRapidGen;

use PHPRapidGen\PHPRapidGen;

class Facade
{
	static function batch( $array, $context )
	{
		$generator = new PHPRapidGen;

		foreach ( $array as $target => $source ) {
			self::convert($source, $target, $context, $generator);
		}
	}

	static function convert( $source, $target, $context, $generator=null )
	{
		if ( empty($generator) ) {
			$generator = new PHPRapidGen;
		}

		$generator->context($context);

		if ( is_array($source) ) {
			$generator->partialContext($source[1]);

			$source = $source[0];
		}

		$info = pathinfo($source);

		$extension = $info['extension'];

		if ( !isset(self::$parsers[$extension]) ) {
			copy($source, $target);

			return;
		}

		$source = pathinfo($source, PATHINFO_FILENAME);

		if ( $partial ) {
			self::$parsers[$extension]->contextPartial($partial);
		}

		return self::parse(
			$info['filename'],
			$extension
		);
	}

	static function parse( $source, $extension=null )
	{
		if ( empty($extension) ) {

		}

		return self::$parsers[$extension]->parse($source);
	}
}
