<?php

namespace PHPRapidGen;

use PHPRapidGen\Parser\PHPParser;
use PHPRapidGen\Parser\SlimPHPParser;
use PHPRapidGen\Parser\HandlebarsParser;

class PHPRapidGen
{
	private $parsers = [];

	public function __construct()
	{
		$this->parsers['json'] = new Parser\SlimPHPParser;
		$this->parsers['handlebars'] = new Parser\HandlebarsParser;
	}

	public function context( $context )
	{
		foreach ( $this->parsers as $parser ) {
			$parser->context($context);
		}
	}

	public function contextPartial( $partial )
	{
		self::$parsers[$extension]->contextPartial($partial);
	}
}
