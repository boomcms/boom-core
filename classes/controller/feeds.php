<?php defined('SYSPATH') or die('No direct script access.');

/**
* Controller for displaying RSS feeds.
* This controller doesn't extend any of the Sledge base controllers because it doesn't need any of their templating code.
* It is it's own little island in the Sledge, refusing to mix with the other controllers.
*
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Feeds extends Kohana_Controller
{
	/**
	* Holds the requested page.
	* @access private
	* @var object
	*/
	private $_page;
	
	/**
	* Method called before the main controller method (the clue's in the name).
	* Handles finding the page which is being RSS'd and checking that RSS feeds are enabled.
	*/
	public function before()
	{
		$url = $this->request->param( 'url' );
		
		// Find the page.
		$page_uri = ORM::factory( 'page_uri' )->where( 'uri', '=', $uri )->find();
		
		if ($page_uri->loaded())
		{
			$this->page = ORM::factory( 'page' )->where( 'page_id', '=', $page_uri->page_id )->find();
			
			if (!$this->page->loaded())
			{
				$this->action_404();
			}	
			
			// Check that the page allows RSS feeds.
			// TODO	
		}
		else
		{
			$this->action_404();
		}	
	}
	
	public function action_rss()
	{
		echo $this->page->title;
		exit;
	}

}

?>
