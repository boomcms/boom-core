<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Sledge_HTML extends Kohana_HTML
{
	/**
	 * This replaces Kohana's HTML::attributes function to improve performance.
	 * Kohana's function sorts the attributes which is unnecessary and causes a performance hit.
	 */
	public static function attributes(array $attributes = NULL)
	{
	    if (empty($attributes))
	        return '';
	
		$compiled = '';
	
	    foreach ($attributes as $key => $value)
	    {
	        if ($value === NULL)
	        {
	            // Skip attributes that have NULL values
	            continue;
	        }
	
	        if (is_int($key))
	        {
	            // Assume non-associative keys are mirrored attributes
	            $key = $value;
	        }
	
	        // Add the attribute value
	        $compiled .= ' ' . $key . '="' . HTML::chars($value) . '"';
	    }
	
	    return $compiled;
	}

	/**
	 * This adds the necessary classes to chunk HTML for them to be picked up by the JS editor.
	 * i.e. it makes chunks editable.
	 *
	 * @param	string	$html		HTML to add classes to.
	 * @param	string	$type		The type of chunk to identify this as. This is picked up by the JS to determine which kind of editor to run
	 * @param	mixed	$target		The target of the chunk. For features this will be a page ID, for asset chunks an asset ID.
	 * @param	mixed	$template	The name of the template used to display this chunk. When editing this chunk this is submitted to the controller to generate a preview of the chunk.
	 * @param	integer	$page		ID of the page that the chunk belongs to.
	 * @return 	string
	 */
	public static function chunk_classes($html, $type, $slotname, $target, $template, $page, $has_content)
	{
		$html = trim( (string) $html);

		$cmsclasses = "{" . 
			$type . " " . 
			$slotname . " " . 
			$target . " " . 
			$template . " " . 
			$page . " " .
			(int) $has_content . "}";

		$pattern1 = '/(.*)class=\"([^\"]*)?chunk-slot([^\"]*)?\"/i';	// chunk-slot already defined, add cms classes on the matching tag
		$pattern2 = '/^(.*?)class=\"([^\"]+)\"/i';			// class attribute already exists in container tag
		$pattern3 = '/^(<[^>]+)>/';					// no class attribute in container tag

		$replacement1 = '$1class="$2 chunk-slot$3 ' . $cmsclasses . '"';
		$replacement2 = '$1class="$2 chunk-slot ' . $cmsclasses . '"';
		$replacement3 = '$1 class="chunk-slot ' . $cmsclasses . '">';

		if (preg_match($pattern1, $html))
		{
			return preg_replace($pattern1, $replacement1, $html, 1);
		}
		elseif (preg_match($pattern2, $html))
		{
			return preg_replace($pattern2, $replacement2, $html, 1);
		}
		elseif (preg_match($pattern3, $html))
		{
			return preg_replace($pattern3, $replacement3, $html, 1);
		}
		else
		{	
			return $html;
		}
	}
}