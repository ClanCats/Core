<?php namespace Orbit;
/**
 * The orbt ship inspector searches for all kind of info about a ship 
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.1
 * @copyright 	2010 - 2015 ClanCats GmbH
 *
 */
class ShipInspector
{
	/**
	 * Inspect an ship at path
	 *
	 * @param string 			$path
	 */
	public static function path( $path )
	{
		return new static( \CCFile::ls( $path.'*' ) );
	}	
	
	/**
	 * An array of file names that are relevant
	 *
	 * @var array
	 */
	protected $relevant_files = array(
		'blueprint.hip',
		'blueprint.json',
		'blueprint.md',
		'README.md',
		'composer.json',
	);
	
	/**
	 * The inspected information
	 *
	 * @var array
	 */
	protected $info = array();
	
	/**
	 * The constructor expects a list of files where to get information
	 *
	 * @param array 			$list
	 * @return void
	 */
	public function __construct( array $filelist = array() )
	{
		foreach( $filelist as $file )
		{
			$basefile = basename( $file );
			
			if ( in_array( $basefile, $this->relevant_files ) )
			{
				$this->assign_info( $basefile, call_user_func( array( $this, 'parse_'. \CCStr::extension( $basefile ) ), file_get_contents( $file ) ) );
			}
		}
	}
	
	/**
	 * Return the files with information
	 *
	 * @return array
	 */
	public function files()
	{
		return array_keys( $this->info );
	}
	
	/**
	 *  Assign and filter information
	 *
	 * @param string 			$basefile
	 * @param array 			$data
	 * @return void
	 */
	protected function assign_info( $basefile, array $data )
	{
		$this->info[$basefile] = $data;
	}
	
	/**
	 * Get a value form the inspector
	 *
	 * @param string 			$key
	 * @param string 			$file 		Get from specific file
	 * @return mixed
	 */
	public function get( $key, $file = null )
	{
		// from specific file
		if ( !is_null( $file ) )
		{
			if ( !isset( $this->info[$file][$key] ) )
			{
				return null;
			}
			
			return $this->info[$file][$key];
		}
		
		// hierarchy
		$hierarchy = $this->relevant_files;
		
		foreach( $hierarchy as $basefile )
		{
			if ( isset( $this->info[$basefile][$key] ) )
			{
				return $this->info[$basefile][$key];
			}
		}
		
		return null;
	}
	
	/**
	 * Parse json string
	 *
	 * @return array
	 */
	protected function parse_json( $json_string )
	{
		return json_decode( $json_string, true );
	}
	
	/**
	 * Parse hip string
	 *
	 * @return array
	 */
	protected function parse_hip( $hip_string )
	{
		return \Hip\Hip::decode( $hip_string );
	}
	
	/**
	 * Parse hip string
	 *
	 * @return array
	 */
	protected function parse_md( $md_string )
	{
		$lines = explode( "\n", $md_string ); $index = 0;
		
		$hip = "";
		
		while( count( $lines ) > $index )
		{
			if ( trim( $lines[$index] ) === '```hip' )
			{
				$index++;
				
				while( trim( $lines[$index] ) !== '```' )
				{
					$hip .= $lines[$index]."\n"; $index++;
				}
			}
			
			$index++;
		}
		
		return $this->parse_hip( $hip );
	}
}