<?php

namespace PHPRapidGen\Helper;

class Basic extends AbstractHelper
{
	private function _array( $input )
	{
		if ( is_object($input) ) {
			$input = get_object_vars($input);
		}

		if ( !is_array($input) && !($input instanceof \Traversable) ) {
			return '';
		}

		$associative = $input !== array_values($input);

		$array = [];
		foreach ( $input as $k => $v ) {
			if ( is_array($v) ) {
				$content = $this->_array($v);
			} else {
				$content = '{"s.String":"'.$v.'"}';

				if ( $associative ) {
					$content .= ',{"s.String":"'.$k.'"}';
				}
			}

			$array[] = '{"ArrayItem":['.$content.']}';
		}

		return '{"Array":[['.implode(',',$array)."]]}";
	}

	private function docbloc( $input )
	{
		if ( is_object($input) ) {
			$input = get_object_vars($input);
		}

		if ( !is_array($input) && !($input instanceof \Traversable) ) {
			return '';
		}

		$lines = [];
		foreach ( $input as $k => $v ) {
			$lines[] = ' * @'.$k.' '.$v;
		}

		return '{"Comment_Doc":["'
			. "/**\n" . implode("\n",$lines)
			. "\n */" . '"]}';
	}

	private function concat( $input )
	{
		return implode($input);
	}
}
