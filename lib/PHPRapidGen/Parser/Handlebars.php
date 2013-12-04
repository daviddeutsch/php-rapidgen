<?php

namespace PHPRapidGen\Parser;

class Handlebars extends AbstractParser
{
	var $engine;

	public function __construct( $options=[] )
	{
		parent::__construct($options);

		$this->engine = new Handlebars(
			array(
			)
		);
	}

	public function context( $context )
	{
		parent::context( new Handlebars_Context($context) );
	}

	public function parse( $source )
	{
		parent::parse($source);

		$this->engine

		return $this->node( $nodeset );
	}
}
