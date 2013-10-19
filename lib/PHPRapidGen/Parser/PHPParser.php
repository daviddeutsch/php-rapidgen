<?php

namespace PHPRapidGen\Parser;

class PHPParser extends AbstractParser
{
	public static $types = [
		's' =>  'Scalar',
		'st' => 'Stmt',
		'h' =>  'Helper',
		't' =>  'Template',
		'n' =>  ''
	];

	public static $options = [
		'helper_class' => 'Helper',
		'template_class' => 'Template',
	];

	public static function type( $type )
	{
		if ( isset(self::$types[$type]) ) {
			return self::$types[$type];
		}

		return $type;
	}

	public static function node( $item )
	{
		$id = array_keys(get_object_vars($item))[0];

		if ( strpos($id, '.') === false ) {
			return self::regularNode( $item, $id, 'Expr', $id );
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
				return self::factoryNode( $c, $item->$id );

				break;
			default:
				return self::regularNode( $item, $id, self::type($t), $c );

				break;
		}
	}

	public static function factoryNode( $type, $item )
	{
		$factory = new \PHPParser_BuilderFactory;

		$f = $factory->$type(self::parse($item[0]));

		if ( !empty($item[1]) ) {
			$sub = get_object_vars($item[1]);

			foreach ( $sub as $k => $v ) {
				if ( is_bool($v) || ($k == $v) ) {
					$f->$k();
				} elseif ( $k == 'stmts' ) {
					$f->addStmts( self::parse($v) );
				} elseif ( $k == 'params' ) {
					$f->addParams( self::parse($v) );
				} elseif ( $k == 'make' ) {
					$f->{'make'.self::parse($v)}();
				} else {
					$f->$k(self::parse($v));
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
					$node->setAttribute($k, [self::parse($vp)]);
				}
			} else {
				$node->setAttribute($k, [self::parse($v)]);
			}
		}

		return $node;
	}

	public static function regularNode( $item, $id, $type, $class )
	{
		$class = self::getClass($class, $type);

		if ( is_array($item->$id) ) {
			$args = [];
			foreach ( $item->$id as $a ) {
				$args[] = self::parse($a);
			}
		} else {
			$args = [self::parse($item->$id)];
		}

		if ( !empty($args) ) {
			$node = $class->newInstanceArgs($args);
		} else {
			$node = $class->newInstance();
		}

		if ( !empty($item->comments) ) {
			$node->setAttribute('comments', [self::parse($item->comments[0])]);
		}

		return $node;
	}

	public static function getClass( $class, $type )
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
