<?php defined('SYSPATH') or die('No direct script access.');

/**
* Error controller.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Error extends Kohana_Controller_Template
{
	/**
	* Set the default template.
	* Used by Controller_Template to know which template to use.
	* @see http://kohanaframework.org/3.0/guide/kohana/tutorials/hello-world#that-was-good-but-we-can-do-better
	* @access public
	* @var string
	*/
	public $template = 'site/standard_template';
	
	/**
	* Error page specific constructor.
	* Ensures that error pages can only be loading internally and not by entering the URL in the browser.
	* Unless we have write permissions for the page.
	*
	*/
 	public function __construct( Request $request, Response $response )
 	{
		//print_r( $request->initial() );die();
		parent::__construct( $request, $response );
		
		if ($request->is_external() && !Model_Permission_Page::may_i( Model_Permission_Page::EDIT, $this->page, $this->person ))
		{
			// An external request and they can't edit the page - send them to the home page instead.
			Request::factory( '/' )->execute();
		}
	}
	
	/**
	* 404 Error handler
	* This should be called as a sub-request where a page object couldn't be found.
	* @see http://kohanaframework.org/3.2/guide/api/Request#factory
	* @return void
	*
	*/
	public function action_404()
	{
		die( 'page not found' );
		/*
		// Determine the requested URI of the *initial* request.
		$initial_uri = Request::initial()->uri();
		
		// We need to work out whether the person has permissions to add pages to the closest parent.
		// If they do we allow them to create a page, otherwise we show the 404 page.
		// If we imagine a situation where the uri is site.com/a/b/c/d
		// We don't yet know whether a page exists at c, b, or a.
		// So we break down the URI and check each part until we find a page.
		
		$uri_parts = explode( "/", $initial_uri );
		$parts_count = count( $uri_parts );
		$writable = false;
		
		// Work out the parent page.
		if ( $parts_count === 1 )
		{
			// We've requested a sub-page of the homepage.
			$page_uri = ORM::factory( 'page_uri' )->where( 'uri', '=', '' );
			$parent = ORM::factory( 'page', $page_uri->page_id );
		}
		else
		{
			$i = $parts_count - 1;
			
			do
			{
				// Get the last element in the array.
				$current = array_pop( $uri_parts );
				
				// Work out the parent URI.
				$parent_uri = implode( "/", $uri_parts );
				
				// See if the parent exists.
				$page_uri = ORM::factory( 'page_uri' )->where( 'uri', '=', $parent_uri );
				$parent = ORM::factory( 'page', $page_uri->page_id );
				
				// This is quicker than recounting the length of the array each time.
				$i--;
					
			} while( $parent->loaded() === false && $i > 0);
		}
		
		// If we found a parent page see if we have permissions to add a page.	
		if ($parent->loaded())
		{
			$writable = Model_Permission_Page::may_i( Model_Permission_Page::ADD, $parent, $this->person );
		}
		
		// Not writable? Show the 404 page.
		if ($writable === false)
		{		*/
			// Set the page property. The controller template will handle rendering etc.
			$page_uri = ORM::factory( 'page_uri' )->where( 'uri', '=', 'error/404' )->find();
			$this->page = ORM::factory( 'page',  $page_uri->page_id );
			
			print_r( $this->page );die();
	/*	}
		else
		{
			// Send another sub-request to the add page method with the URI we're adding the page to.
			Request::factory( 'cms/page/add' )->method( Request::POST )->post( array( 'uri' => $initial_uri ) )->execute();
		}*/
	}
	
	public function action_403()
	{
		if (! (Auth::instance()->logged_in()) )
		{
			Request::factory( '/cms/login' )->execute();
		}
		else
		{
			$this->subtpl_main = 'error/403';
		}	
	}
}

?>
