<?php

namespace PHPRapidGen\Template;

class AbstractTemplate
{
	static $path;

	public function __construct()
	{

	}

	public static function get( $template )
	{
		if ( strpos('.', $template) ) {
			$rel = str_replace('.', '/', $template);
		}
	}
}
