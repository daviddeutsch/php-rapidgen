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

		if ( !empty($this->context_partial) ) {
			$context = $this->resolveContext($this->context_partial);
		} else {
			$context = $this->context;
		}

		return $this->engine->render(
			basename($source, ".handlebars"),
			$context
		);
	}
}
