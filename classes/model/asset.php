<?php

/**
* Asset Model
* @package Asset
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class asset_Model extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $table_name = 'asset';
	protected $has_one = array( 
		'version'			=> array('model' => 'asset_v' ),
		'asset_type'		=> array('model' => 'asset_type' ),
		'encoding'			=> array('model' => 'asset_encoding' )
	);
	protected $has_many = array( 
		'versions'	=> array('model' => 'asset_v', 'foreign_key' => 'id'),
	);
	protected $load_with = array( 'version' );
	protected $foreign_key = array( 'version' => 'active_vid' );
	
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
	*
	* @uses asset_decorator_Model::getDecoration
	*/
	public function __construct( $key ) {
		parent::__construct( $key );
		
		return asset_decorator_Model::getDecoration( $this );
	}
	
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
}