<?php

namespace PHPRapidGen\Parser;

class Handlebars extends AbstractParser
{
	/**
	 * @var \Handlebars_Engine
	 */
	var $engine;

	public function __construct( $options=[] )
	{
		parent::__construct($options);

		$this->engine = new \Handlebars_Engine();
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
