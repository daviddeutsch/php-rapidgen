<?php

namespace PHPRapidGen\Parser;
use Handlebars\Handlebars;

class HandlebarsParser extends AbstractParser
{
	/**
	 * @var Handlebars
	 */
	var $engine;

	public function __construct( $options=[] )
	{
		parent::__construct($options);

		$this->engine = new Handlebars();
	}

	public function parse( $source )
	{
		parent::parse($source);

		return $this->engine->render(
			basename($source, ".handlebars"),
			$this->context
		);
	}
}
