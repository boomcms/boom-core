<?php defined('SYSPATH') or die('No direct script access.');

/**
* Search controller.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Search extends Controller_Site
{
	/**
	* Search index method.
	* Performs all searching etc.
	*
	*/
	public function action_index()
	{
		$query = Arr::get( $_REQUEST, 'search' );
		$query = strip_tags ( $query );
		$query = trim( $query );
		
		$results = array();
		$this->template->subtpl_main->results = $results;
		$this->template->subtpl_main->count = count( $results );
		
		if ($query === '')
		{
			// They didn't enter a search query. Tell them off.
			$this->template->subtpl_main->message = 'Please enter a search term';
		}	
	}

}

?>
