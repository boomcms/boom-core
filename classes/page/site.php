<?php defined('SYSPATH') or die('No direct script access.');

/**
* Site Page class.
* Provides site specific features to page objects.
* @package Page
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Page_Site extends Page
{
	/**
	* Retrieve an editable slot for the page.
	* 
	* @param string $type The slot type.
	* @param string $slotname
	* @return string The HTML to display the slot
	*/	
	public function get_slot($type, $slotname, $editable = false)
	{
		$slot = $this->_page->get_slot( $type, $slotname );
		
		if ($slot->loaded())
			echo $slot->show();		
	}
}