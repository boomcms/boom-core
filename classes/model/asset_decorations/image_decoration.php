<?php

/*
* @see http://en.wikipedia.org/wiki/Decorator_pattern
*
* @package Asset
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/

class image_decoration_Model extends asset_decorator_Model {
	/**
	* No idea what this does. I assume it scales an image - which is why it's in here and called what it is. But that's as far I've got.
	*/	
	public function scale( $w, $h, $mw='', $mh='' ) {
		foreach( array('w','h') as $v) {
			$m = "m{$v}";
			if (${$v} > ${$m} && ${$m}) {
				$o = ($v == 'w') ? 'h' : 'w';
				$r = ${$m} / ${$v}; 
				${$v} = ${$m}; 
				${$o} = ceil(${$o} * $r); 
			} 
		}
		
		return array( $w, $h);
	}
}

?>