<?php

/**
* Slideshow chunk model
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Chunk_Slideshow extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'chunk_slideshow';
	protected $_primary_key = 'chunk_id';
	protected $_has_many = array(
		'slides' => array( 'model' => 'slideshowimage', 'foreign_key' => 'chunk_id' ),
	);
	protected $_load_with = array( 'slides' );
	
	private $_asset_ids = array();
	
	
	public function show( $template = 'circles' )
	{	
		if (!$template)
		{
			$template = 'circles';
		}
		
		if ($this->loaded())
		{
			$v = View::factory( "site/slots/slideshow/$template" );
			$v->chunk = $this;
			
			$v->title = $this->title;
			$v->slides = $this->slides->find_all();	
			
			return $v;
		}	
	}
	
	/**
	* Return an array of asset ids which are used in this slideshow.
	*
	* @todo This needs optimizing so that the database query is only done once per request.
	*/
	public function get_asset_ids()
	{
		if (empty( $this->_asset_ids ))
		{
			foreach( $this->slides->find_all() as $slide )
			{
				$this->_asset_ids[] = $slide->asset_id;
			}
		}
		
		return $this->_asset_ids;
	}
	
	/**
	* Accepts and array of asset IDs and sets them as the slides.
	*
	*/
	public function set_asset_ids( array $assets )
	{
		$this->_asset_ids = $assets;
	}
	
	public function show_default()
	{
		return "Click on me to add a slideshow here";		
	}
	
	public function save( Validation $validation = null )
	{
		$return = parent::save( $validation );
		
		// Remove all existing slides.
		DB::query( Database::DELETE, "delete from slideshowimages where chunk_id = " . $this->pk() );
				
		foreach( $this->_asset_ids as $asset )
		{
			// Check the asset exists.
			$asset = ORM::factory( 'asset', $asset );
			
			if ($asset->loaded())
			{
				$slide = ORM::factory( 'slideshowimage' )->values( array(
					'chunk_id'	=>	$this->chunk_id,
					'asset_id'	=>	$asset->pk()
				))->create();
			}
		}
		
		return $return;
	}
}

?>
