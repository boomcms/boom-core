<?php defined('SYSPATH') or die('No direct script access.');

/**
* Site slot decorator.
* 
* @package Slots
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Slot_Site extends Slot {
	/**
	* Returns false if no slot is loaded.
	* As site slots aren't editable they don't have defaults which may be changed.
	* A site slot therefore returns false if the child slot wasn't loaded to indicate that the slot doesn't exist.
	*
	* @param Slot $slot
	* @return mixed
	*/
	protected function __construct( Slot $slot )
	{
		if (!$slot->loaded())
		{
			return false;
		}
		else
		{
			return parent::__construct( $slot );
		}
	}
}

?>
