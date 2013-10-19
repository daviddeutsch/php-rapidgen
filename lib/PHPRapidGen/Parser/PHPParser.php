<?php

namespace PHPRapidGen\Parser;

class PHPParser extends AbstractParser
{
	private static $types = [
		's' =>  'Scalar',
		'st' => 'Stmt',
		'h' =>  'Helper',
		't' =>  'Template',
		'n' =>  ''
	];

	public static $options = [
		'helper_class' => 'Basic',
		'template_class' => 'Template',
	];

	private function type( $type )
	{
		if ( isset(self::$types[$type]) ) {
			return self::$types[$type];
		}

		return $type;
	}

	private function node( $item )
	{
		$id = array_keys(get_object_vars($item))[0];

		if ( strpos($id, '.') === false ) {
			return $this->regularNode( $item, $id, 'Expr', $id );
		}

		list($t, $c) = explode('.', $id, 2);

		switch ( $t ) {
			case 't':
				return self::$template->{$c}( $item->$id );

				break;
			case 'h':
				return self::$helper->{$c}( $item->$id );

				break;
			case 'f':
				return $this->factoryNode( $c, $item->$id );

				break;
			default:
				return $this->regularNode( $item, $id, self::type($t), $c );

				break;
		}
	}

	private function factoryNode( $type, $item )
	{
		$factory = new \PHPParser_BuilderFactory;

		$f = $factory->$type($this->parse($item[0]));

		if ( !empty($item[1]) ) {
			$sub = get_object_vars($item[1]);

			foreach ( $sub as $k => $v ) {
				if ( is_bool($v) || ($k == $v) ) {
					$f->$k();
				} elseif ( $k == 'stmts' ) {
					$f->addStmts( $this->parse($v) );
				} elseif ( $k == 'params' ) {
					$f->addParams( $this->parse($v) );
				} elseif ( $k == 'make' ) {
					$f->{'make'.$this->parse($v)}();
				} else {
					$f->$k($this->parse($v));
				}
			}
		}

		$node = $f->getNode();

		if ( empty($item[2]) ) {
			return $node;
		}

		$attr = get_object_vars($item[2]);

		foreach ( $attr as $k => $v ) {
			if ( is_array($v) ) {
				foreach ( $v as $vp ) {
					$node->setAttribute($k, [$this->parse($vp)]);
				}
			} else {
				$node->setAttribute($k, [$this->parse($v)]);
			}
		}

		return $node;
	}

	private function regularNode( $item, $id, $type, $class )
	{
		$class = self::getClass($class, $type);

		if ( is_array($item->$id) ) {
			$args = [];
			foreach ( $item->$id as $a ) {
				$args[] = $this->parse($a);
			}
		} else {
			$args = [$this->parse($item->$id)];
		}

		if ( !empty($args) ) {
			$node = $class->newInstanceArgs($args);
		} else {
			$node = $class->newInstance();
		}

		if ( !empty($item->comments) ) {
			$node->setAttribute('comments', [$this->parse($item->comments[0])]);
		}

		return $node;
	}

	private static function getClass( $class, $type )
	{
		$classname = 'PHPParser_';

		if ( strpos($class, 'Comment') !== false ) {
			$classname .= $class;
		} else {
			$classname .= 'Node_';

			if ( !empty($type) ) {
				$classname .= $type.'_';
			}

			$classname .= $class;
		}

		return new \ReflectionClass($classname);
	}
}
