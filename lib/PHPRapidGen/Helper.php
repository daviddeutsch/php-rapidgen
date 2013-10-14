<?php

class PHPParser_Node_Helper
{
	public static function _array( $input )
	{
		if (is_object($input)) {
			$input = get_object_vars($input);
		}

		$buffer = '';
		if (is_array($input) || $input instanceof Traversable) {
			$is_assoc = $input !== array_values($input);

			$array = [];
			foreach ( $input as $k => $v ) {
				if ( is_array($v) ) {
					$content = self::_array($v);
				} else {
					if ( $is_assoc ) {
						$content = '{"s.String":"'.$v.'"},{"s.String":"'.$k.'"}';
					} else {
						$content = '{"s.String":"'.$v.'"}';
					}
				}

				$array[] = '{"ArrayItem":['.$content.']}';
			}

			$buffer = '{"Array":[['.implode(',',$array)."]]}";
		}

		return $buffer;
	}

	public static function docbloc( $input )
	{
		$buffer = '';
		if (is_array($input) || $input instanceof Traversable) {
			foreach ( $input as $k => $v ) {
				$lines[] = ' * @'.$k.' '.$v;
			}

			$buffer = '{"Comment_Doc":["' . "/**\n".implode("\n",$lines)."\n */" . '"]}';
		}

		return $buffer;
	}

	public static function concat( $input )
	{
		return implode( $input );
	}
}
