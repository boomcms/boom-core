<?php

/*
* @see http://en.wikipedia.org/wiki/Decorator_pattern
*
* @package Asset
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/

class video_decoration_Model extends asset_decorator_Model {
	/**
	* Used for encoding a video, I assume.
	*/
	public function encode_status() {
		$video = O::fa('asset_type')->find_by_name('video');
		$h264_type = O::fa('asset_type')->find_by_parent_rid_and_name($video->rid,'mp4');
		$flv_type = O::fa('asset_type')->find_by_parent_rid_and_name($video->rid,'x-flv');
		$vp3_type = O::fa('asset_type')->find_by_parent_rid_and_name($video->rid,'ogg');
		$vp8_type = O::fa('asset_type')->find_by_parent_rid_and_name($video->rid,'webm');

		$encodings = array();
		
		foreach (Kohana::config('encoder', false, false) as $key => $value) {
			if (preg_match('/^encode_video_([a-z0-9]+)$/',$key,$m) && $value === true) {
				$type_var = $m[1].'_type';

				if (!$asset->encoding_exists(${$type_var}->rid)) {
					echo "0";
					exit;
				}
			}
		}

		echo "1";
		exit;
	}
}

?>