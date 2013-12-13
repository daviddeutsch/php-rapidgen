<?php

namespace PHPRapidGen;

use PHPRapidGen\Parser\PHPParser;
use PHPRapidGen\Parser\SlimPHPParser;
use PHPRapidGen\Parser\HandlebarsParser;

class PHPRapidGen
{
	private $parsers = [];

	public function __construct( $context=null )
	{
		$this->parsers['json'] = new Parser\SlimPHPParser;
		$this->parsers['handlebars'] = new Parser\HandlebarsParser;

		if ( !empty($context) ) {
			return $this->context($context);
		}

		return $this;
	}

	public function context( $context )
	{
		foreach ( $this->parsers as $parser ) {
			$parser->context($context);
		}

		return $this;
	}

	public function convert($source, $target)
	{
		$info = pathinfo($source);

		$extension = $info['extension'];

		if ( !isset($this->parsers[$extension]) ) {
			copy($source, $target);

			return;
		}

		$source = pathinfo($source, PATHINFO_FILENAME);

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
