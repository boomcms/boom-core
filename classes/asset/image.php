<?php

/*
* Image decorator for assets.
* Handles image specific asset features
*
* @package Asset
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Asset_Image extends Asset {	
	public function show( $width = null, $height = null, $quality = null )
	{
		$image = Image::factory( ASSETPATH . $this->instance()->pk() );
		
		if ($width || $height)
			$image->resize( $width, $height );
			
		header('Content-type: ' . $image->mime);
		return $image->render(null, $quality );		
	}
	
	public function preview()
	{
		return $this->show();
	}
}

?>