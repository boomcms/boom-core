<?php

/*
* Video asset decorator
*
* @package Asset
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/

class Asset_Video extends Asset
{	
	public function show() {
		return "<iframe src='" . $this->instance()->filename . "' frameborder='0' class='video' allowfullscreen></iframe>";
	}
	
	public function preview()
	{
		return $this->show();
	}
}

?>