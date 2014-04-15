<?php

/**
 * Helper class with static methods for creating UI elements.
 *
 */
abstract class Boom_BoomUI
{
	/**
	 * Returns the HTML for a styled button
	 *
	 * @param string $type The type of button, used to determine the button icon
	 * @param string $text Button text
	 * @param array $attrs HTML attributes for the button
	 * @return string
	 */
	public static function button($type, $text, array $attrs = array())
	{
		// Add the important b-button class to the button attributes which has all the CSS rules targeted at it.
		isset($attrs['class']) || $attrs['class'] = '';
		$attrs['class'] = trim($attrs['class'] . ' b-button');

		// Make the button text the title of the button.
		$attrs['title'] = $text;

		$attrs_string = HTML::attributes($attrs);
		$type = $type? " b-button-icon-$type" : '';

		return "<button $attrs_string><span class='b-button-icon $type'></span><span class='b-button-text'>$text</span></button>";
	}
}