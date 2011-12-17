<?php defined('SYSPATH') or die('No direct script access.');

/**
* CMS Slot decorator.
* Extends the default slot show() method to allow the slow to be edited.
*
* @package Slots
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Slot_Cms extends Slot {
	/**
	* CMS slot show method.
	*
	* @return string Editable slot HTML.
	*/
	public function show()
	{
		return "<div>" . $this->slot->show() . "</div>";
	}
}

?>