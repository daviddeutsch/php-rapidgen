<?php

namespace PHPRapidGen;

use PHPRapidGen\PHPRapidGen;
use PHPRapidGen\GeneratorCascade;

class Facade
{
	/**
	 * @var GeneratorCascade;
	 */
	private static $cascade;

	static function batch( $array, $context )
	{
		if ( empty(self::$cascade) ) {
			self::$cascade = new GeneratorCascade;
		}

		foreach ( $array as $target => $source ) {
			self::convert($source, $target, $context);
		}
	}

	static function convert( $source, $target, $context )
	{
		if ( empty(self::$cascade) ) {
			self::$cascade = new GeneratorCascade;
		}

		self::$cascade->current()->context($context);

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

	static function parseChild( $source, $context )
	{
		if ( empty(self::$cascade) ) {
			self::$cascade = new GeneratorCascade;
		}

	}
}
