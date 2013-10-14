<?php

class PHPRapidGen_Facade
{
	static $types = [
		's' =>  'Scalar',
		'st' => 'Stmt',
		'h' =>  'Helper',
		't' =>  'Template',
		'n' =>  ''
	];

	static $options = [
		'helper_class' =>
	]

	public static function configure( $options )
	{

	}

	public static function type( $type )
	{
		if ( isset( self::$types[$type] ) ) {
			return self::$types[$type];
		}

		return $type;
	}

	public static function parse( $nodeset )
	{
		if ( is_array($nodeset) ) {
			$array = [];
			foreach ( $nodeset as $node ) {
				$array[] = self::parse($node);
			}

			return $array;
		}

		if ( !is_object($nodeset) ) {
			return $nodeset;
		}

		return self::node( $nodeset );
	}

	public static function node( $item )
	{
		$ident = array_keys(get_object_vars($item))[0];

		if ( strpos($ident, '.') === false ) {
			return self::regularNode( $item, $ident, 'Expr', $ident );
		}

		$c = explode('.', $ident, 2);

		if ( $c[0] == 'f' ) {
			return self::factoryNode( $c[1], $item->$ident );
		}

		return self::regularNode( $item, $ident, self::type($c[0]), $c[1] );
	}

	public static function factoryNode( $type, $item )
	{
		$factory = new PHPParser_BuilderFactory;

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

		if ( empty( $item[2] ) ) {
			return $node;
		}

		$attr = get_object_vars( $item[2] );

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

	public static function regularNode( $item, $ident, $type, $class )
	{
		$class = self::getClass( $class, $type );

		if ( is_array( $item->$ident ) ) {
			$args = [];
			foreach ( $item->$ident as $a ) {
				$args[] = self::parse($a);
			}
		} else {
			$args = [self::parse($item->$ident)];
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

		return new ReflectionClass($classname);
	}
}
