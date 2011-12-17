<?php defined('SYSPATH') or die('No direct script access.');

/**
* People controller
* Pages for managing people.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/

class Controller_Cms_People extends Controller {
	
	public function action_add()
	{
		
		
		
	}
	
	public function action_edit()
	{
		$id = $this->request->param('id');
		$id = preg_replace( "/[^0-9]+/", "", $id );
		
		
		
		
	}
	
	/**
	* People manager default page.
	* Displays the people manager template with an array of people.
	*
	* @return void
	*/
	public function action_index()
	{
		$orderby = Arr::get( 'request', 'orderby', 'date' );
		$order = Arr::get( 'request', 'order', 'asc' );
		
		$people = ORM::factory( 'person' )->order_by( $orderby, $order )->find_all();
		
		$this->template->subtpl_main = View::factory( 'cms/pages/people/index' );
		$this->template->subtpl_main->people = $people;
		
		echo $this->template;
	}
	
}

?>