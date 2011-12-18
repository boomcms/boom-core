<?php defined('SYSPATH') or die('No direct script access.');

/**
* Slot Class.
* @package Slots
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/

abstract class Slot extends ORM {
	
	public static function factory( $type, $page_id, $slotname )
	{
		$slot = parent::factory( $type )->where( 'page_id', '=', $page_id )->and_where( 'slotname', '=', $slotname )->find();
		
		switch ($mode)
		{
			case 'cms':
				return new Slot_Cms( parent::factory( $slot ) );
				break;
			default:
				return new Slot_Site( parent::factory( $slot ) );
		}	
	}
	
}

?>
