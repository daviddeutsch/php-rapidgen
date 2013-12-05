<?php

namespace PHPRapidGen\Helper;

class AbstractHelper
{
	protected $helpers = [];

	public function __construct()
	{
		foreach ( get_class_methods($this) as $method ) {
			if ( strpos($method, '__') === 0 ) continue;

			if ( strpos($method, '_') === 0 ) {
				$n = substr($method, 1);
			} else {
				$n = $method;
			}

			$helpers[$n] = $method;
		}
	}

	public function __call( $name, $args )
	{
		if ( !in_array($name, $this->helpers) ) return null;

		return call_user_func(
			array( 'self', $this->helpers[$name] ),
			$args[0]
		);

	}
}
