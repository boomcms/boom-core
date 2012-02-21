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
	public function get_slot($type, $slotname, $htmlbefore = '<div>', $htmlafter = '</div>')
	{
		$slot = $this->_page->get_slot( $type, $slotname );
		
		// Bit of a hack - the 3rd paramater for feature boxes is the posititon of the feature box at the moment.
		// This all needs changing...
		if ($type == 'feature')
		{
			$template = $htmlbefore;
			
			if ($template == '<div>')
			{
				$template = null;
			}
			$htmlbefore = '<div>';
		}
		
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
			

			$htmlbefore = $this->addcmsclasses( $htmlbefore, $type, $slotname, $target );
			$html = $htmlbefore . $slot->data->show( @$template ) . $htmlafter;
		}
		else
		{
			$htmlbefore = $this->addcmsclasses( $htmlbefore, $type, $slotname, '' );
			$html = $htmlbefore . ORM::factory( "chunk_$type" )->show_default( @$template ) . $htmlafter;
		}
		
		echo $html;
	}	
	
	/**
	* Addcmsclasses method.
	* Ripped from sledge2. Needs a rewrite.
	* This makes the slot editable.
	*/
	private function addcmsclasses($html, $slottype, $slotname, $target) {
		if ($slottype != 'slideshow')
		{
			$disablededitoroptions = '';
			$editor = 'tinyMCE';
			$cmsclasses = '';
		
			if ($slottype == "text") {
				$cmsclasses = "{" . 
				$slottype . " " . 
				$slotname . 
				//$disablededitoroptions . 
				//$editor .
				"}";
			} elseif ($slottype == "linkset") {
				$cmsclasses = "{" . 
				$slottype . " " . 
				$slotname . " " .
				//$data['template'] .
				"}";
			} else if ($slottype == 'asset-caption') {
				$cmsclasses = "{" .
				$slottype . " " .
				$slotname . " " .
				//$disablededitoroptions .
				//$editor .
				"}";
			} else {
				$cmsclasses = "{" . 
				$slottype . " " . 
				$slotname . " "; 
				if ($target) {
					$cmsclasses .= $target; 
				} else {
					$cmsclasses .= "0";
				}
				//$cmsclasses .= $data['template'];
				$cmsclasses .= "}";
			}

			$pattern1 = "/^(.*?)class=\"([^\"]*)?chunk-slot([^\"]*)?\"/i";	// chunk-slot already defined, add cms classes on the matching tag
			$pattern2 = "/^(.*?)class=\"([^\"]+)\"/i";			// class attribute already exists in container tag
			$pattern3 = "/^(<[^>]+)>/";					// no class attribute in container tag

			$replacement1 = "$1class=\"$2 chunk-slot$3 {$cmsclasses}\"";
			$replacement2 = "$1class=\"$2 chunk-slot {$cmsclasses}\"";
			$replacement3 = "$1 class=\"chunk-slot {$cmsclasses}\">";
				
			if (preg_match($pattern1, $html)) {
				return preg_replace($pattern1, $replacement1, $html, 1);
			} else if (preg_match($pattern2, $html)) {
				return preg_replace($pattern2, $replacement2, $html, 1);
			} else if (preg_match($pattern3, $html)) {
				return preg_replace($pattern3, $replacement3, $html, 1);
			} else {	
				return $html;
			}
		}
		else
		{
			return $html;
		}
	}
}
