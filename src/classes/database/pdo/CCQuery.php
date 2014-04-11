<?php namespace CC\Database;
/**
 * The Query class
 *
 * @package 		ClanCats-Framework
 * @author     		Mario Döring <mariodoering@me.com>
 * @version 		0.4
 * @copyright 		2010 - 2013 ClanCats GmbH 
 *
 */
class Pdo_CCQuery {
	
	/**
	 * select query
	 *
	 * @param string	$table
	 * @return static
	 */
	public static function select( $table ) {
		return new static( static::SELECT, $table );
	}
	
	/**
	 * insert query
	 *
	 * @param string	$table
	 * @return static
	 */
	public static function insert( $table ) {
		return new static( static::INSERT, $table );
	}
	
	/**
	 * update query
	 *
	 * @param string	$table
	 * @return static
	 */
	public static function update( $table ) {
		return new static( static::UPDATE, $table );
	}
	
	/**
	 * insert or update
	 *
	 * @param string	$table
	 * @return static
	 */
	public static function insert_or_update( $table ) {
		return new static( static::INSORUP, $table );
	}
	
	/**
	 * delete query
	 *
	 * @param string	$table
	 * @return static
	 */
	public static function delete( $table ) {
		return new static( static::DELETE, $table );
	}
	
	/*
	 * SELECT query type
	 */
	const SELECT = 1;
	
	/*
	 * INSERT query type
	 */
	const INSERT = 2;
	
	/*
	 * UPDATE query type
	 */
	const UPDATE = 3;
	
	/*
	 * DELETE query type
	 */
	const DELETE = 4;
	
	/*
	 * INSORUP query type
	 */
	const INSORUP = 5;
	
	/*
	 * current query type
	 */
	public $query_type;
	
	/*
	 * the database table
	 */
	public $table;
	
	/*
	 * does the query return a bool result
	 */
	public $bool = false;
	
	/*
	 * group the result 
	 */
	public $group_result = false;
	
	/*
	 * make a ignore
	 */
	public $ignore = false;
	
	/*
	 * fetch as array / object
	 */
	public $fetch_as = 'array';
	 
	/*
	 * the prepared parameters
	 */
	public $parameters = array();
	
	/*
	 * for automtic parameters
	 */
	private $param_counter = 1;
	
	/*
	 * the fields to select
	 */
	public $fields = array( '*' );
	
	/*
	 * insert and update values
	 */
	public $values = array();
	
	/*
	 * the joins
	 */
	public $joins = array();
	
	/*
	 * query wheres
	 */
	public $wheres;
	
	/*
	 * query order
	 */
	public $orders;
	
	/*
	 * query groups
	 */
	public $groups;
	
	/*
	 * the query offset
	 */
	public $offset = 0;
	
	/*
	 * the query limit
	 */
	public $limit = 1;
	
	/**
	 * the query constructor
	 *
	 * @param int		$type
	 * @param string	$table
	 * @return void
	 */
	public function __construct( $type, $table ) {
		
		// set the constructor vars
		$this->query_type = $type;
		$this->table = $table;
		
		// type
		switch ( $type ) {
			case static::SELECT:
			case static::INSERT:
				$this->bool = false;
			break;
			case static::UPDATE:
			case static::DELETE:
				$this->bool = true;
			break;			
		}
	}
	
	/**
	 * fetch as array
	 * @return self
	 */
	public function as_array() {
		$this->fetch_as = 'array'; return $this;
	}
	
	/**
	 * fetch as object
	 * @return self
	 */
	public function as_object() {
		$this->fetch_as = 'object'; return $this;
	}
	
	/**
	 * should the query group the results by a key
	 *
	 * @param mixed		$by
	 * @return self
	 */
	public function group_result( $by = 'id' ) {
		$this->group_result = $by; return $this;
	}
	
	/** 
	 * should the query only return bool results
	 *
	 * @param bool	$bool
	 * @return self
	 */
	public function return_bool( $bool = true ) {
		$this->bool = $bool; return $this;
	}
	
	/**
	 * insert into or insert ignore
	 *
	 * @param bool		$ignore
	 * @return self
	 */
	public function ignore( $ignore ) {
		$this->ignore = $ignore; return $this;
	}
	
	/**
	 * set parameters
	 *
	 * @param array		$params
	 * @param bool		$merge
	 * @return self
	 */
	public function params( $params = array(), $merge = true ) {
		if ( $merge ) {
			$this->parameters = array_merge( $this->parameters, $params );
		} else {
			$this->parameters = $params;
		}
		
		return $this;
	}
	
	/**
	 * set just one parameter
	 *
	 * @param string	$key
	 * @param string	$value
	 * @return self
	 */
	public function param( $key, $value ) {
		$this->parameters[$key] = $value; return $this;
	}
	
	/**
	 * parameterize an array
	 *
	 * @param mixed 	$data
	 * @return array
	 */
	protected function parameterize( $data ) {
		
		if ( !is_array( $data ) ) {
			$param = ':v_'.$this->param_counter;
			$this->parameters[$param] = $data;
			$this->param_counter++;
			return $param;
		}
		
		foreach( $data as $key => $val ) {
			$data[$key] = ':v_'.$this->param_counter;
			$this->parameters[':v_'.$this->param_counter] = $val;
			$this->param_counter++;
		}
		return $data;
	}
	
	/**
	 * Set the select fields
	 *
	 * @param mixed		$fields
	 * @return self
	 */
	public function fields( $fields = 'all', $deli = ', ' ) {
		
		if ( $fields == 'all' || $fields == '*' || is_null( $fields ) ) {
			$fields = array( '*' );
		}
		
		if ( !is_array( $fields ) ) {
			
			$explode = explode( $deli, $fields );
			
			if ( count( $explode ) <= 0 ) {
				$fields = array( $fields );
			}
			else {
				$fields = $explode;
			}
		} else {
			foreach( $fields as $key => $field ) {
				if( is_string( $key ) ) {
					if ( is_array( $field ) ) {
						foreach( $field as $col ) {
							$fields[] = $key.'.'.$col;
						}
						unset( $fields[$key] );
					} else {
						$fields[$key] = $field.' AS `'.$key.'`';
					}
				}
			}
		}
		
		$this->fields = $fields; 
		
		return $this;
	}
	
	/**
	 * set insert or update values
	 *
	 * @param array		$fields 	
	 * @param array		$data
	 * @param bool		$secure
	 * @return self
	 */
	public function values( $fields, $data = null, $secure = false ) {
		
		if ( is_null( $fields ) ) {
			return $this;
		}
		
		if ( !is_null( $data ) && $this->fields == array('*') ) {
			$this->fields = $fields; 
			$fields = $data; 
		}
		
		// check if we have multiple data sets
		if ( !is_array( $fields[key($fields)] ) ) {
			$fields = array( $fields );
		}
		
		// do we have some fields
		if ( $this->fields == array('*') ) {
			$this->fields = array_keys( $fields[key($fields)] );
		}
		
		// clean keys
		foreach( $fields as $key => $item ) {
			if ( $secure ) {
				$fields[$key] = $this->parameterize( array_values( $item ) );
			}
			else {
				$fields[$key] = array_values( $item );
			}
		}
		
		// add the items
		$this->values = array_merge( $this->values, $fields );
		
		return $this;
	}
	
	/**
	 * this is just a secure values wrapper
	 */
	public function s_values( $fields, $data = null ) {
		return $this->values( $fields, $data, true );
	}
	
	/**
	 * Join a table
	 *
	 * @param string 	$table
	 * @param string	$t1_col
	 * @param string	$t2_col
	 * @return self
	 */
	public function join( $table, $t1_col = null, $t2_col = null ) {
		
		if ( strpos( $table, ' ' ) !== false && $t1_col == null && $t2_col == null ) {
			$this->joins[] = $table; return $this;
		}
		
		if ( is_null( $t1_col ) ) {
			$t1_col = $this->table.'.'.$table.'_id';
		}
		
		if ( is_null( $t2_col ) ) {
			$t2_col = $table.'.id';
		}
		
		$this->joins[] = array(
			$table,
			$t1_col,
			$t2_col
		);
		
		return $this;
	}
	
	/**
	 * where query: <$type> <$column> <$param1> <$param2>
	 * example: WHERE name IN array( 'mario', 'john' )
	 *
	 * @param string	$column
	 * @param mixed		$param1
	 * @param mixed		$param2
	 * @param string	$type
	 * @param bool		$secure
	 * @return self
	 */
	public function where( $column, $param1 = null, $param2 = null, $type = 'and', $secure = false ) {
		
		// if the argument is an array
		if ( is_array( $column ) ) {
			foreach( $column as $key => $val ) {
				$this->where( $key, $val, null, $type, $secure );	
			}
			return $this;
		}
		
		// if param2 not set 1 is =
		if ( is_null( $param2 ) ) {
			$param2 = $param1; $param1 = '=';
		}
		
		if ( is_array( $param2 ) ) {
			$param2 = array_unique( $param2 );
		}
		
		if ( $secure ) {
			$param2 = $this->parameterize( $param2 );
		}
		
		if ( empty( $this->wheres ) ) {
			$type = 'where';
		}
		
		$this->wheres[] = array(
			$type,
			$column,
			$param1,
			$param2,
		);
		
		return $this;
	}
	
	/**
	 * this is just a secure where wrapper
	 */
	public function s_where( $column, $param1 = null, $param2 = null, $type = 'and' ) {
		return $this->where( $column, $param1, $param2, $type, true );
	}
	
	/**
	 * this is just a secure OR where wrapper
	 */
	public function s_or_where( $column, $param1, $param2 = null ) {
		return $this->where( $column, $param1, $param2, 'or', true );
	}
	
	/**
	 * this is just a secure OR where wrapper
	 */
	public function s_and_where( $column, $param1, $param2 = null ) {
		return $this->where( $column, $param1, $param2, 'and', true );
	}
	
	
	/**
	 * or where query, the same as normal where function just
	 * with a fixed where type
	 *
	 * @return self
	 */
	public function or_where( $column, $param1, $param2 = null ) {
		return $this->where( $column, $param1, $param2, 'or' );
	}
	
	/**
	 * and where query, the same as normal where function just
	 * with a fixed where type
	 *
	 * @return self
	 */
	public function and_where( $column, $param1, $param2 = null ) {
		return $this->where( $column, $param1, $param2, 'and' );
	}
	
	/**
	 * order stuff
	 *
	 * @param mixed 	$cols
	 * @param string	$order
	 * @return self
	 */
	public function order_by( $cols, $order = 'asc' ) {
		
		if ( is_string( $cols ) ) {
			$this->orders[] = array(
				$cols,
				$order
			);
			
			return $this;
		}
		
		if ( is_array( $cols ) ) {
			foreach( $cols as $key => $col ) {
				if ( is_string( $key ) ) {
					$this->orders[] = array(
						$key,
						$col
					);
				} else {
					$this->orders[] = array(
						$col,
						$order
					);
				}
			}
		}
		
		return $this;
	}
	
	/**
	 * set the query limit to page
	 *
	 * @param int	$limit
	 * @param int 	$limit2
	 * @return void
	 */
	public function page( $page, $size = 25 ) {
		
		$this->limit = (int) $size;
		$this->offset = ( $size * $page ) - $size;
		
		return $this;
	}
	
	/**
	 * set the query limit
	 *
	 * @param int	$limit
	 * @param int 	$limit2
	 * @return void
	 */
	public function limit( $limit, $limit2 = null ) {
		if ( !is_null( $limit2  ) ) {
			$this->offset = (int) $limit;
			$this->limit  = (int) $limit2;
		}
		else {
			$this->limit = $limit;
		}
		return $this;
	}
	
	/**
	 * execute tha quarrryz
	 *
	 * @param string	$name
	 * @param mixed 	returns false if it fails
	 */
	public function run( $name = null ) {
		
		// get a database instance
		$db = CCPDO::instance( $name );
		
		// execute the query
		$sth = $db->execute( $db->build( $this ), $this->parameters );
		
		// is a select query
		if ( $this->query_type == static::SELECT ) {
		
			// fetch mode
			if ( $this->fetch_as == 'array' ) {
				$args = \PDO::FETCH_ASSOC;
			}
			elseif ( $this->fetch_as == 'object' ) {
				$args = \PDO::FETCH_OBJ;
			}
			
			// fetch group
			//if ( $this->group_result !== false ) {
			//	$args |= \PDO::FETCH_GROUP;
			//}
		
			$result = $sth->fetchAll( $args );
			
			// clean grouped result
			if ( $this->group_result !== false ) {
				$grouped_result = array();
				foreach( $result as $key => $res ) {
					$grouped_result[$res[$this->group_result]] = $res;	
				}
				$result = $grouped_result;
			}
			
			
			// return just the first if limit is 1
			if ( $this->limit == 1 ) {
				$key = key($result);
				if ( array_key_exists( $key, $result ) ) {
					$result = $result[$key];
				}
				else {
					return array();
				}
			}
		}
		else {
			if ( $this->query_type == static::INSERT ) {
				// return the new created id
				return $db->last_insert_id();
			}
			// return the affected count
			return $sth->rowCount();
		}
		
		return $result;
	}
	
	/**
	 * debugging function
	 */
	public function _get_query() {
		// get a database instance
		$db = CCPDO::instance( $name );
		
		// execute the query
		return $db->build( $this );
	}

	/**
	 * find something, means select one record by the id
	 *
	 * @param int		$id
	 * @param string	$db
	 * @param string	$key
	 * @return mixed
	 */
	public function find( $id, $key = 'id', $db = null ) {
		return $this->where( $key, ':id' )->param( 'id', $id )->limit( 0, 1 )->run( $db );
	}
	
	/**
	 * execute and get the first record
	 *
	 * @param string	$key
	 * @param string	$db
	 * @return mixed
	 */
	public function first( $key, $db = null ) {
		return $this->order_by( $key, 'asc' )->limit( 0, 1 )->run( $db );
	}
	
	/**
	 * execute and get the last record
	 *
	 * @param string	$key
	 * @param string	$db
	 * @return mixed
	 */
	public function last( $key, $db = null ) {
		return $this->order_by( $key, 'desc' )->limit( 0, 1 )->run( $db );
	}
	
	/**
	 * execute and get just on column
	 *
	 * @param string	$column
	 * @param string	$db
	 * @return mixed
	 */
	public function column( $column, $db = null ) {
		return \CCArr::get( $column, $this->fields( $column )->limit( 0, 1 )->run( $db ) );
	}
	
	/**
	 * execute a count query
	 *
	 * @param string 	$db
	 * @return int
	 */
	public function count( $db = null ) {
		return (int) $this->column( "COUNT(*)" );
	}
}