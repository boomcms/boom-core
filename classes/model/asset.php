<?php

/**
* Asset Model
* @package Asset
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Asset extends ORM_Versioned implements Interface_Taggable {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'asset';
	protected $has_one = array( 
		'asset_type'		=> array('model' => 'asset_type' ),
		'encoding'			=> array('model' => 'asset_encoding' )
	);
	protected $_belongs_to = array(
		'version'  => array( 'model' => 'version_asset', 'foreign_key' => 'active_vid' ), 	
		'chunk_asset'		=> array('model' => 'chunk_asset', 'foreign_key' => 'asset_id' ),
	);
	protected $_load_with = array( 'version' );
	
	/**
	* Value for asset status published.
	* @var integer
	*/
	const STATUS_UNPUBLISHED = 1;	
	
	/**
	* Value for asset status published.
	* @var integer
	*/
	const STATUS_PUBLISHED = 2;
	
	/**
	* Array of tags which have been applied to the current asset.
	* @see self::tags()
	* @access private
	* @var array
	*/
	private $_tags;
	
	/**
	* Returns a human readable asset status (currently published or unpublished).
	*
	* @return string Asset status
	*/
	public function getStatus() {
		switch( $this->status ) {
			case self::STATUS_PUBLISHED:
				return 'Published';
				break;
			case self::STATUS_UNPUBLISHED:
				return 'Unpublished';
				break;
			default:
				throw new Kohana_Exception( 'Asset has unknown asset status value: ' . $this->status );
		}			
	}
	
	/**
	* Apply a tag to the current asset.
	* Required by the taggable interface.
	* Creates a relationship with the tag table in the tagged_objects table.
	*
	* @uses self::$_tags
	* @uses Model_Tagged_Object
	* @param Model_Tag $tag The tag to be applied.
	* @return bool True on success, false on failure
	*/
	public function apply_tag( Model_Tag $tag )
	{
		$values = array( 
			'tag_id'		=> $tag->pk(),
			'object_type'	=> Model_Tagged_Object::OBJECT_TYPE_ASSET,
			'object_id'		=> $this->pk()
		);
		
		try
		{
			$tagged = ORM::factory( 'tagged_object' )->values( $values )->create();
		}
		catch( DatabaseException $e )
		{
			return false;
		}
		
		// Add the new relationship to self::$_tags if it's been loaded.
		if (is_array( $this->_tags ))
		{
			$this->_tags = array_push( $this->_tags, $tag );
		}		
	}
	
	/**
	* Determines which tags have been applied to an asset.
	* Required by the taggable interface.
	*
	* @todo Inherit parent tags of applied tags through the tag MPTT tree.
	*
	* @uses Model_Tagged_Object
	* @uses self::$_tags
	* @return array Array of Model_Tag ojects.
	*/
	public function tags()
	{
		if ($this->_tags === null)
		{
			$this->_tags = ORM::factory( 'tag' )
							->join( 'tagged_object', 'inner' )
							->on( 'tag_id', '=', 'tag.id' )
							->where( 'object_type', '=', Model_Tagged_Object::OBJECT_TYPE_ASSET )
							->where( 'object_id', '=', $this->pk() )
							->find_all()
							->as_array();
		}
		
		return $this->_tags;
	}
}