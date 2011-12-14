<?php defined('SYSPATH') or die('No direct script access.');

/**
* Search controller.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Search extends Controller_Template
{
	/**
	* Search index method.
	* Performs all searching etc.
	*
	*/
	public function action_index()
	{
		die( "search" );
		$query = Arr::get( $_REQUEST['search'] );
		$query = strip_Tags ($query );
		$query = trim( $query );
		
		if ($query === '')
		{
			// They didn't enter a search query. Tell them off.
			$this->template->message = 'Please enter a search term';
		}
		else
		{
			$fromlevel = Arr::get( $_GET, 'section' );
			$fromlevel = (!empty($fromlvel) && $fromlevel != 'all') ? (int) $fromlevel : null;
		
			$page_no = Arr::get( $_GET, 'p', 1 );
			
			// Use the search class to do the searching.
			$s = new Search( 
				'page',
				$query,
				null, 
				array('site_search_priority'=>'desc', 'rank'=>'desc' , 'page_v.visiblefrom_timestamp'=>'desc' )
			);
			
			// 
			$count = $s->get_count();
			
		}	
	}

}

?>
