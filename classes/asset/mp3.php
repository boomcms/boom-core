<?php

/*
* MP3 asset decorator
*
* @package Asset
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/

class Asset_Mp3 extends Asset
{	
	public function show() {
		header('Content-type: audio/mpeg');
		header("Content-Length: ".filesize(ASSETPATH . $this->instance()->pk() ));
		ob_clean();
		flush();
		readfile(ASSETPATH . $this->instance()->filename);
	}
	
	
	public function preview()
	{
		echo "<a href='/asset/" . $this->instance()->id . "'>Click here to listen to MP3</a>";
	}
}

?>