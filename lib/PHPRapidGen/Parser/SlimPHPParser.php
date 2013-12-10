<?php

namespace PHPRapidGen\Parser;

class SlimPHPParser extends AbstractParser
{
	public $options = [
		'helper_class' => 'Helper\Basic',
		'template_class' => 'Helper\Template',
	];

	/**
	 * @var array shorthands for PHP Parser types
	 */
	private $types = [
		's' =>  'Scalar',
		'st' => 'Stmt',
		'h' =>  'Helper',
		't' =>  'Template',
		'n' =>  ''
	];

	/**
	 * @var \PHPParser_BuilderFactory
	 */
	private $factory;

	/**
	 * @var \PHPParser_PrettyPrinter_Default
	 */
	private $printer;

	public function __construct( $options=[] )
	{
		parent::__construct($options);

		$this->factory = new \PHPParser_BuilderFactory;

		$this->printer = new \PHPParser_PrettyPrinter_Default();
	}

	/**
	 * Convert a JSON file path into PHP code
	 *
	 * @param $source string path to the json file
	 *
	 * @return string|void
	 */
	public function parse( $source )
	{
		return $this->parseJSON(
			json_decode( file_get_contents($source) )
		);
	}

	/**
	 *
	 * @param mixed $tree
	 * @param array $nodes
	 *
	 * @return string
	 */
	public function parseTree( $tree, $nodes=[] )
	{
		foreach ( $tree as $node ) {
			$nodes[] = $this->node($node);
		}

		return $this->printer->prettyPrint($nodes);
	}

	/**
	 * Multi Node Resolution
	 *
	 * @param $item
	 *
	 * @return object|string
	 */
	protected function node( $item )
	{
		if ( !is_object($item) ) {
			return parent::node($item);
		}

		$id = array_keys(get_object_vars($item))[0];

		if ( strpos($id, '.') === false ) {
			if ( $id === 'c' ) {
				return $this->resolveContext($item->$id);
			} else {
				return $this->regularNode( $item, $id, 'Expr', $id );
			}
		}

		list($t, $c) = explode('.', $id, 2);

		switch ( $t ) {
			case 't':
				return $this->template->{$c}($item->$id);

				break;
			case 'h':
				return $this->helper->{$c}($item->$id);

				break;
			case 'f':
				return $this->factoryNode($c, $item->$id);

				break;
			default:
				return $this->regularNode( $item, $id, self::type($t), $c );

				break;
		}
	}

	/**
	 * Factory Node resolution
	 *
	 * @param $type
	 * @param $item
	 *
	 * @return mixed
	 */
	private function factoryNode( $type, $item )
	{
		$f = $this->getFactory($type, $this->node($item[0]));

		if ( !empty($item[1]) ) {
			$sub = get_object_vars($item[1]);

			foreach ( $sub as $k => $v ) {
				if ( is_bool($v) || ($k == $v) ) {
					$f->$k();
				} elseif ( $k == 'stmts' ) {
					$f->addStmts( $this->node($v) );
				} elseif ( $k == 'params' ) {
					$f->addParams( $this->node($v) );
				} elseif ( $k == 'make' ) {
					$f->{'make'.$this->parse($v)}();
				} else {
					$f->$k($this->node($v));
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
					$node->setAttribute($k, [$this->node($vp)]);
				}
			} else {
				$node->setAttribute($k, [$this->node($v)]);
			}
		}

		return $node;
	}

	/**
	 * Basic PHP Parser node type
	 *
	 * @param $item object
	 * @param $id
	 * @param $type
	 * @param $class
	 *
	 * @return object
	 */
	private function regularNode( $item, $id, $type, $class )
	{
		$class = $this->getClass($class, $type);

		if ( is_array($item->$id) ) {
			$args = [];
			foreach ( $item->$id as $a ) {
				$args[] = $this->node($a);
			}
		} else {
			$args = [$this->node($item->$id)];
		}

		if ( !empty($args) ) {
			$node = $class->newInstanceArgs($args);
		} else {
			$node = $class->newInstance();
		}

		if ( !empty($item->comments) ) {
			$node->setAttribute('comments', [$this->node($item->comments[0])]);
		}

		return $node;
	}

	/**
	 * Converts the PHP Parser type shorthand into its proper form
	 *
	 * @param $type string
	 *
	 * @return mixed long form of type
	 */
	private function type( $type )
	{
		if ( isset($this->types[$type]) ) {
			return $this->types[$type];
		}

		return $type;
	}

	/**
	 * Build a factory call from a call type
	 *
	 * @param $type string
	 * @param $name string
	 *
	 * @return object generated by PHPParser factory
	 */
	private function getFactory( $type, $name )
	{
		return $this->factory->$type($name);
	}

	/**
	 * Build a full PHP Parser class name
	 *
	 * @param $class
	 * @param $type
	 *
	 * @return \ReflectionClass
	 */
	private function getClass( $class, $type )
	{
		$name = 'PHPParser_';

		if ( strpos($class, 'Comment') !== false ) {
			$name .= $class;
		} else {
			$name .= 'Node_';

			if ( !empty($type) ) {
				$name .= $type.'_';
			}

			$name .= $class;
		}

		return new \ReflectionClass($name);
	}

	private function resolveContext( $key )
	{
		if ( !is_array($key) ) {
			$key = explode('.', $key);
		}

		if ( empty($key) ) return false;

		$return = $this->context;

		foreach ( $key as $k ) {
			if ( is_object($return) ) {
				if ( property_exists($return, $k) ) {
					$return = $return->$k;
				}
			} elseif ( is_array($return) ) {
				if ( isset($return[$k]) ) {
					$return = $return[$k];
				}
			}
		}

		return $return;
	}
}
