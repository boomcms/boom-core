<?php

/**
* Defines a decorator class for use with assets. 
* Assets can be one of many different types - video, image, application etc.
* Each type of asset will need different methods for things like retrieval.
* This class therefore creates an 'interface' of which there are different sub-types which we can use for performing actions on the asset.
* This is an example of the decorator pattern.
* @see http://en.wikipedia.org/wiki/Decorator_pattern
*
* @package Asset
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* @todo Most of the methods in here are direct copy and paste from the previous Asset library. They need to be refactored and documented.
*
*/

abstract class Asset {
	/**
	* @access protected
	* @var object
	* Stores an instance of the asset_Model class which we are providing an interface to.
	*/
	protected $_asset;
	
	public function __construct( Model_Asset $asset )
	{
		$this->_asset = $asset;
	}
	
	/**
	* Used to initialise the decorator.
	* Determines which decoration we need from the asset_type name
	* @param string $type The type of asset
	* @param object $asset The asset we're decorating
	* @return Asset
	*/
	public static function factory( $type, Model_Asset $asset ) {
		switch( $type ) {
			case 'image':
				return new Asset_Image( $asset );
				break;
			case 'video':
				return new Asset_Video( $asset );
				break;
			case 'mp3':
				return new Asset_MP3( $asset );
				break;
			case 'pdf':
				return new Asset_PDF( $asset );
				break;				
			default:
				return new Asset_Default( $asset );
		}
	}
	
	public function instance()
	{
		return $this->_asset;
	}
	
	/**
	* Method to show the asset
	*/
	abstract function show();
	
	/**
	* Method to preview the asset in a page/
	*/
	abstract function preview();	
}