<?php defined('SYSPATH') or die('No direct script access.');

/**
* @package Slots
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/

abstract class Slot_Decorator extends Slot {
	protected $slot;
	
	function __construct( Slot $s )
	{
		$this->slot = $s;	
	}	
	
	public function getSlotname()
	{
		return $this->slot->slotname;
	}
}

?>
