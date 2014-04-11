<?php namespace CC\Database;
/**
 * PDO Database driver
 *
 * @package 		ClanCats-Framework
 * @author     		Mario Döring <mariodoering@me.com>
 * @version 		0.4
 * @copyright 		2010 - 2013 ClanCats GmbH 
 *
 */
class Pdo_CCDriver {
	
	/*
	 * the raw connection object
	 */
	public $connection;
	
	/*
	 * the string used for the connection
	 */
	protected $connection_string;
	
	/**
	 * connect to database
	 *
	 * @param array 	$conf
	 * @return bool
	 */
	public function connect( $conf ) {
		
		$rpl = array();
		
		foreach( $conf as $key => $value ) {
			if ( is_string( $value ) ) {
				$rpl[ '{'.$key.'}' ] = $value;
			}
		}
		
		// replace the conncetion string with the 
		$con_str = \CCArr::replace( $rpl, $this->connection_string );
		
		try {
			$this->connection = new \PDO( 
				$con_str, 
				$conf['user'], 
				$conf['pass'], 
				$this->pdo_attributes( $conf ) 
			);
		}
		catch( \PDOException $e ) {
			throw new \CC\Core\CCException( $e->getMessage() ); return false;
		}
		
		// let pdo throw exceptions
		$this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		
		return true;
	}
	
	/**
	 * return the connection attributes
	 *
	 * @param array 	$conf
	 * @return array
	 */
	protected function pdo_attributes( $conf ) {
		return array();
	}
	
	/**
	 * build an SQL query string from a query object
	 * 
	 * @param CCQuery 	$query
	 * @return string
	 */
	public function build( $query ) {
		
		/*
		 * query type
		 */
		switch ( $query->query_type ) {
			case Pdo_CCQuery::SELECT:
				return $this->build_select( $query );
			break;
			case Pdo_CCQuery::INSERT:
				return $this->build_insert( $query );
			break;
			case Pdo_CCQuery::UPDATE:
				return $this->build_update( $query );
			break;
			case Pdo_CCQuery::INSORUP:
				return $this->build_insorup( $query );
			break;
			case Pdo_CCQuery::DELETE:
				return $this->build_delete( $query );
			break;			
		}
	}
	
	/**
	 * build the where string
	 *
	 * @return string
	 */
	protected function build_where( $query ) {
		
		if ( !is_array( $query->wheres ) ) {
			return "";
		}
		
		$query_str = "";
		
		// where
		foreach( $query->wheres as $where ) {
			
			if ( is_array( $where[3] ) ) {
				foreach( $where[3] as $key => $val ) {
					if ( is_string( $val ) ) {
						if ( substr($val, 0, 1 ) != ':' ) {
							$where[3][$key] = "'".$val."'";
						}
						else {
							$where[3][$key] = $val;
						}
					} 
				}
				
				if ( empty( $where[3] ) ) {
					$where[3] = array( 0 );
				}
				
				// @fix: empty array values
				$where[3] = array_filter($where[3], 'strlen');
				
				$where[3] = "(".implode( $where[3], ',' ).")";
			}
			elseif ( is_string( $where[3] ) ) {
				if ( substr( $where[3], 0, 1 ) != ':' ) {
					$where[3] = "'".$where[3]."'";
				}
			}
			else {
				$where[3] = '';
			}
			
			$query_str .= " ".strtoupper($where[0])." ".$where[1]." ".strtoupper($where[2])." ".$where[3];
		}
		
		return $query_str;
	}
	
	/**
	 * build order by
	 *
	 * @return string
	 */
	protected function build_order_by( $query ) {
		$query_str = "";
		
		// order by
		if ( !empty( $query->orders ) ) {
			
			$query_str .= " ORDER BY";
			
			foreach( $query->orders as $order ) {
				$query_str .= ' '.$order[0].' '.strtoupper($order[1]).',';
			}
			
			$query_str = substr( $query_str, 0, -1 ); 
		}
		
		return $query_str;
	}
	
	/**
	 * build limit
	 *
	 * @return string
	 */
	protected function build_limit( $query ) {
		$query_str = "";
		
		// limit
		if ( !is_null( $query->limit ) ) {
			$query_str .= ' LIMIT '.$query->offset.', '.$query->limit;
		}
		
		return $query_str;
	}
	
	/**
	 * build the select query
	 * 
	 * @param CCQuery	$query
	 * @return string
	 */
	protected function build_select( $query ) {
		
		// select
		$query_str = "SELECT ";
		
		// fields
		$query_str .= implode( $query->fields, ', ' );
		
		// from
		$query_str .= " FROM ".$query->table;
		
		// joins
		foreach( $query->joins as $join ) {
			if ( !is_array( $join ) ) {
				$query_str .= " ".$join;
			} else {
				$query_str .= " INNER JOIN ".$join[0]." ON ".$join[1]."=".$join[2];
			}
		}
		
		// build where
		$query_str .= $this->build_where( $query );
		
		// build where
		$query_str .= $this->build_order_by( $query );		
		
		// build limit
		$query_str .= $this->build_limit( $query );
		
		return $query_str;
	}
	
	/**
	 * build an insert query
	 * 
	 * @param CCQuery	$query
	 * @return string
	 */
	protected function build_insert( $query ) {
		// INSERT INTO tbl_name (a,b,c) VALUES(1,2,3),(4,5,6),(7,8,9);
		// select
		if ( $qurey->ignore ) {
			$query_str = "INSERT IGNORE `".$query->table."` ";
		} else {
			$query_str = "INSERT INTO `".$query->table."` ";
		}
		
		// fields
		$query_str .= "(`".implode( $query->fields, '`, `' )."`) VALUES ";
		
		// values
		foreach( $query->values as $item ) {
			
			foreach( $item as $key => $val ) {
				if ( substr( $val, 0, 1 ) != ':' || $val == 'null' ) {
					$item[$key] = "'".$val."'";
				}
			}
			
			$query_str .= "(".implode( $item, ', ' )."),";
		}
		
		$query_str = substr( $query_str, 0, -1 );
		
		return $query_str;
	}
	
	/**
	 * build an update query
	 * 
	 * @param CCQuery	$query
	 * @return string
	 */
	protected function build_update( $query ) {
		
		// reset field keys
		$query->fields = array_values( $query->fields );
		$query->values = array_values( $query->values );
		
		$query_str = "UPDATE `".$query->table."` SET ";
		
		// values
		foreach( $query->values as $item ) {
			
			foreach( $item as $key => $val ) {
				if ( substr( $val, 0, 1 ) != ':' || $val == 'null' ) {
					$val = "'".$val."'";
				}
				
				$query_str .= "`".$query->fields[$key]."` = ".$val.",";
			}
			
			
		}
		
		$query_str = substr( $query_str, 0, -1 );
		
		// build where
		$query_str .= $this->build_where( $query );
		
		// build limit
		if ( !is_null( $query->limit ) ) {
			$query_str .= ' LIMIT '.$query->limit;
		}

		return $query_str;
	}
	
	/**
	 * build an update query
	 * 
	 * @param CCQuery	$query
	 * @return string
	 */
	protected function build_insorup( $query ) {
		
		//INSERT INTO table (a,b,c) VALUES (1,2,3)
		//ON DUPLICATE KEY UPDATE c=c+1;
		
		$query_str = "INSERT INTO `".$query->table."` ";
		
		// fields
		$query_str .= "(`".implode( $query->fields, '`, `' )."`) VALUES ";
		
		// values
		foreach( $query->values as $item ) {
			
			foreach( $item as $key => $val ) {
				if ( substr( $val, 0, 1 ) != ':' || $val == 'null' ) {
					$item[$key] = "'".$val."'";
				}
			}
			
			$query_str .= "(".implode( $item, ', ' )."),";
		}
		
		$query_str = substr( $query_str, 0, -1 );
		
		$query_str = " ON DUPLICATE KEY UPDATE ";
		
		// values
		foreach( $query->values as $item ) {
			
			foreach( $item as $key => $val ) {
				if ( substr( $val, 0, 1 ) != ':' || $val == 'null' ) {
					$val = "'".$val."'";
				}
				
				$query_str .= "`".$query->fields[$key]."` = ".$val.",";
			}
			
			
		}
		
		$query_str = substr( $query_str, 0, -1 );
		
		return $query_str;
	}
	
	/**
	 * build an delete query
	 * 
	 * @param CCQuery	$query
	 * @return string
	 */
	protected function build_delete( $query ) {
		
		// delete
		$query_str = "DELETE FROM `".$query->table."` ";
		
		// build where
		$query_str .= $this->build_where( $query );
		
		// build where
		$query_str .= $this->build_order_by( $query );		
		
		// build limit
		if ( !is_null( $query->limit ) ) {
			$query_str .= ' LIMIT '.$query->limit;
		}
		
		return $query_str;
	}
}