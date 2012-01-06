<?php defined('SYSPATH') or die('No direct script access.');

/**
* CMS Page class.
* Provides CMS specific features to page objects.
* @package Page
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Page_CMS extends Page
{
	
	/**
	* Retrieve an editable slot for the page.
	* 
	* @param string $type The slot type.
	* @param string $slotname
	* @return string The HTML to display the slot
	*/	
	public function get_slot($type, $slotname, $htmlbefore = '', $htmlafter = '')
	{
		$slot = parent::get_slot( $type, $slotname );
		
		echo $htmlbefore, "(Editable)", $slot->show(), $htmlafter;		
	}	
}