<?php defined('SYSPATH') or die('No direct script access.');

/**
* iSlot interface
* Ensures that all slot classes (models and cms/site decorators) implement the same methods for retrieving slot details.
* @package Slots
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
interface iSlot
{

	public function show();
	public function getSlotname();
	public function getTitle();
	public function getPage();
	public function __toString();
}

?>
