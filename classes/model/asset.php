<?php

/**
* Asset Model
* @package Asset
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Asset extends ORM_Versioned {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'asset';
	protected $has_one = array( 
		'asset_type'		=> array('model' => 'asset_type' ),
		'encoding'			=> array('model' => 'asset_encoding' )
	);
	protected $_belongs_to = array(
		'chunk_asset'		=> array('model' => 'chunk_asset', 'foreign_key' => 'asset_id' ),
	);
	
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