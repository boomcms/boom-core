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
	* Holds the auth instance.
	* @access protected
	* @var object
	*/
	protected $auth;
	
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
		
		// Check that the datbase exists, if not we offer to create it.
		try{
			$this->db = Database::instance();
			$this->db->connect();
		}
		catch (Database_Exception $e)
		{
			// Is it a database not existing error?
			if (preg_match( '/Unable to connect to (.*) database &quot;(.*)&quot; does not exist/', $e->getMessage(), $matches ))
			{
				$v = View::factory( 'setup/database/tpl_create' );
				$v->dbname = $matches[2];
				echo $v->render();
				exit();
			}
			
			// It's some other error which we don't worry about here.
			throw $e;
		}
		
		// TODO: get the person, not an empty object
		$this->person = ORM::factory( 'person' );
		
		// Load the relevant page object.
		$this->page = ORM::factory( 'page', $this->request->uri() );
		
		$this->auth = Auth::instance();
		
		// If the page wasn't found by URI load the 404 page.
		// TODO: check that the requested URI wasn't the 404 page or we end up in an infinate loop.
		//if (!$this->page->loaded())
		//	Request::factory( 'error/404' )->execute();
				
		// Load the relevant template object.
		$this->subtpl_main = $this->page->version->template;
		
		$this->actual_person = ORM::factory( 'person' );
	}
}

?>