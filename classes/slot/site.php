<?php defined('SYSPATH') or die('No direct script access.');

/**
* @package Slots
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/

class Slot_Site extends Slot_Decorator {
	/**
	* Returns false if no slot is loaded.
	* As site slots aren't editable they don't have defaults which may be changed.
	* A site slot therefore returns false if the child slot wasn't loaded to indicate that the slot doesn't exist.
	*
	* @param Slot $slot
	* @return mixed
	*/
	function __construct( Slot $slot )
	{
		if (!$this->loaded())
		{
			return false;
		}
		else
		{
			return parent::__construct( $slot );
		}
	}
	
	public function show()
	{
		return $this->slot->show();
	}

}

?>
