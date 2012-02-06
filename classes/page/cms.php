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
		$slot = $this->_page->get_slot( $type, $slotname );
		
		if ($slot->loaded())
			$html = $htmlbefore . $slot->show() . $htmlafter;
		else
			$html = $htmlbefore . $slot->show_default() . $htmlafter;
			
		if ($type == 'asset')
			$target = $slot->asset_id;
		else
			$target = 0;
		
		echo $this->addcmsclasses( $html, $type, $slotname, $target );
	}	
	
	/**
	* Addcmsclasses method.
	* Ripped from sledge2. Needs a rewrite.
	* This makes the slot editable.
	*/
	private function addcmsclasses($html, $slottype, $slotname, $target) {
		$disablededitoroptions = '';
		$editor = 'tinyMCE';
		$cmsclasses = '';
		
		if ($slottype == "text") {
			$cmsclasses = "{" . 
			$slottype . " " . 
			$slotname . " " . 
			$disablededitoroptions . 
			$editor .
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
			$disablededitoroptions .
			$editor .
			"}";
		} else {
			$cmsclasses = "{" . 
			$slottype . " " . 
			$slotname . " "; 
			if ($target) {
				$cmsclasses .= $target . " "; 
			} else {
				$cmsclasses .= "0 ";
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
}