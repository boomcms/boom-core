<?php

/**
* Base class for Postboxes.
* Contains a factory method and methods common to all postboxes.
*
* WTF are postboxes I hear you cry!
* Well, they're a way of handling forms. The obvious thing to call this would have been Form but Kohana already has one of those
* So I went for postbox - because they handle POST forms. geddit?
*
* Forms which use a postbox should be submitted via POST and have an input called postbox with the name of the postbox they use.
* The forms postbox will handle input validation and contain code to 'complete' the form such as sending an email.
*
* The point of this bunch of classes is that it allows us to handle a specific form (such as the contact us form) regardless of the URL of the URL of the page which that form is on.
* If we had, for instance, a contact-us controller then we'd need to update routing rules every time that the contact-us page was moved in the CMS. That's going to make the form break if a cms user moves the page, as they don't have access to routing rules.
*
* @package Postbox
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* 
*/
abstract class Postbox
{
	/**
	* Factory method to create a postbox.
	* Uses reflection to check that the postbox can be instantiated.
	*
	* @param string $postbox The name of the desired postbox.
	* @return Postbox An instance of the Postbox class.
	*/
	public static function factory( $postbox )
	{
		$class = new ReflectionClass( "Postbox_$postbox" );
		
		if ($class->isInstantiable())
		{
			return $class->newInstance();
		}
	}
	
	/**
	* Method to validation the form input.
	*/
	abstract function validate();
	
	/**
	* Do something with the form data.
	* e.g. Send an email, post to Campaign Monitor, display search results
	*/
	abstract function complete();
}

?>