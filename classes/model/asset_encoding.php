<?php

/**
* @package Asset
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class asset_encoding_Model extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $belongs_to = array('model' => 'asset');

	public function get_filename() {
		if (!isset($this->type)) $this->type = O::fa('asset_type',$this->asset_type_rid);
		return $this->asset_rid . "." . $this->type->extension;
	}

	public function get_dimensions() {
		if (trim(`uname`) == 'Linux') {
			// No need for path on Linux, ffmpeg availablility on path tested elsewhere 
			$path = '';
		} else {
			$path = '/opt/local/bin/';
		}

		$data = shell_exec($path."ffmpeg -i ".Kohana::config('core.assetpath').$this->get_filename()." 2>&1");

		if (preg_match('/Stream.*?Video: .*?, ([0-9]+)x([0-9]+)/m',$data,$m)) {
			$obj = new stdClass;
			$obj->width = $m[1];
			$obj->height = $m[2];
			return $obj;
		}

		return false;
	}
}
?>
