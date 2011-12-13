<?php defined('SYSPATH') or die('No direct script access.');

/**
* Sledge base controller.
* All Sledge controllers should inherit from this controller to ensure authentication etc.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/

class Controller extends Kohana_Controller {
	/**
	* The current user.
	* @access protected
	* @var object
	*/
	protected $person;
	
	/**
	* The real current user.
	* Used for Hoop users to pose as a different user.
	* @access protected
	* @var object
	*/
	protected $actual_person;
	
	/**
	* The requested page.
	* @access protected
	* @var object
	*/
	protected $page;
	
	/**
	* The template for the requested page.
	* @access protected
	* @var object
	*/
	protected $subtpl_main;
	
	/**
	* Sledge controller construct
	* Performs authentication etc.
 	* @param   Request   $request  Request that created the controller
	* @param   Response  $response The request's response
 	* @return  void
 	*/
 	public function __construct( Request $request, Response $response ) {
		// Inherit all Kohana's cool controller stuff.
		parent::__construct( $request, $response );
		
		//$this->person = ORM::factory( 'person' )->find_by_emailaddress( 'guest@hoopassociates.co.uk' );
		
		// Load the relevant page object.
		$this->page = ORM::factory( 'page', $this->request->uri() );
		
		// If the page wasn't found by URI load the 404 page.
		if (!$this->page->loaded())
			Request::factory( 'error/404' )->execute();
				
		// Load the relevant template object.
		$this->subtpl_main = $this->page->version->template;
		
		$this->actual_person = ORM::factory( 'person' );
	}
}

?>