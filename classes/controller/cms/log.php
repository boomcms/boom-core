<?php

/**
* Controller for displaying CMS logs such as the activity log.
*
* @package Sledge
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates Ltd
*/
class Controller_Cms_Log extends Controller_Template
{
	
	/**
	* Handles displaying the activity log.
	*
	*/
	public function action_activity()
	{
		$last24hours = ORM::factory( 'activitylog' )->where( 'timestamp', '>=', time() - 86400 )->orderby( 'timestamp', 'desc' )->find_all();
		$last50 = ORM::factory( 'activitylog' )->orderby( 'timestamp', 'desc' )->limit( 50 )->find_all();
		
		$this->template->subtpl_main = View::factory( 'cms/templates/tpl_activitylog' );
		$this->template->subtpl_main->last24hours = $last24hours;
		$this->template->subtpl_main->last50 = $last50;		
	}
	
	/**
	* Displays records from the search log.
	*/
	public function action_search()
	{
		$searches = DB::select( 'log_entry', array('count("*")', 'count') )->from( 'log_search' )->group_by( 'log_entry' )->order_by( 'count', 'desc' )->execute();
		$total = DB::select( array('count("*")', 'count') )->from( 'log_search' )->execute();
		
		$this->template->subtpl_main = View::factory( 'cms/templates/tpl_log_search' );
		$this->template->subtpl_main->searches = $searches;
		$this->template->subtpl_main->total = $total;
		
		echo $this->template;
	}
}


?>