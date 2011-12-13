<?php

/*
* @see http://en.wikipedia.org/wiki/Decorator_pattern
*
* @package Asset
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/

class mp3_decoration_Model extends asset_decorator_Model {	
	public function get() {
		$file = Kohana::config('core.assetpath') . $this->_asset->current_version->filename;
		
		if (!file_exists($file)) {
			throw new Kohana_Exception('sledge.asset_doesnt_exist_on_fs', $file);
		}

		header('Content-type: audio/mpeg');
		header("Content-Length: ".filesize($file));
		ob_clean();
		flush();
		readfile($file);
	}
}

?>