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
		
		// What page are we looking at?
		$page = Arr::get( $_REQUEST, 'page', 1 );
		
		// Find and count results.
		$query = ORM::factory( 'page' )->where( 'deleted', '=', 'false' );
		
		$count = $query->count_all();
		$results = $query->limit( 10 )->offset( ($page - 1) * 10)->order_by( 'page.id', 'asc' )->find_all();
		
		$this->template->subtpl_main->results = $results;
		$this->template->subtpl_main->count = $count;
		
		// Configure pagination template.
		$total_pages = ceil( $count / 10 );
		$pagination = View::factory( 'pagination/search_results' );
		$pagination->current_page = $page;
		$pagination->total_pages = $total_pages;
		$pagination->base_url = $this->page->url();
		$pagination->previous_page = ($page > 1)? $page -1 : 0;
		$pagination->next_page = ($page < $total_pages)? $page +1 : 0;
		
		$this->template->subtpl_main->pagination = $pagination;
	}

}

?>
