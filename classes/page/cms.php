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
	public function get_slot($type, $slotname, $template)
	{
		$slot = $this->_page->get_slot( $type, $slotname );	
		
		if ($slot->loaded())
		{
			
			switch ($type)
			{
				case 'asset':
					$target = $slot->data->asset_id;
					break;
				case 'feature':
					$target = $slot->data->target_page_id;
					break;
				default:
					$target = 0;
			}
			
			$html = $slot->data->show( $template );//, $type, $slotname, $target );
		}
		else
		{
			$html = ORM::factory( "chunk_$type" )->show_default( @$template );
			$target = 0;
		}
		
		if ($type == 'feature' || $type == 'asset')
		{
			echo "<div class='chunk-slot {", $type, " ", $slotname, " ", $target, "}'>", $html, "</div>";
		}
		else
		{
			echo "<div class='chunk-slot {", $type, " ", $slotname, "}'>", $html, "</div>";
		}
	}	
}
