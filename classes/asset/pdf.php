<?php

/*
* PDF decorator for assets.
*
* @package Asset
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Asset_Pdf extends Asset {	
	public function show()
	{
		header('Content-type: application/pdf');
		header('Content-Disposition: inline; filename="' . $this->instance()->filename . '"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize(ASSETPATH . $this->instance()->filename));
		header('Accept-Ranges: bytes');

		@readfile(ASSETPATH . $this->instance()->pk() );	
	}
	
	public function preview()
	{
		echo "<a href='/asset/" . $this->instance()->id . "'>Click here to view PDF</a>";
	}
}

?>