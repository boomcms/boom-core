<?php defined('SYSPATH') or die('No direct script access.');

/**
* Sledge ORM MPTT extension.
* This extension removes table locking from the ORM_MPTT class as it doesn't work with postgresql (Kohana supports MySQL by default).
* This is a hack which is somewhat undesirable, but the plan is to go back to MySQL soonish anyway so this is only temporary.
*
* @package ORM
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class ORM_Mptt extends Kohana_ORM_Mptt {
	
	protected function lock()
	{
		return true;
	}	
	
	protected function unlock()
	{
		return true;
	}
	
}
	
?>