<?php defined('SYSPATH') or die('No direct script access.');

/**
* Tag controller
* Pages for managing tags.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Cms_Tags extends Controller_Cms {	
	public function before()
	{		
		parent::before();
		
		if (!$this->person->can( 'manage tags' ))
			Request::factory( 'error/403' )->execute();
		
		$this->template->title = 'Tag Manager';
		$subtpl_topbar = View::factory( 'ui/subtpl_tag_manager_topbar' );
		
		View::bind_global( 'subtpl_topbar', $subtpl_topbar );
	}

	
	/**.
	* Displays the tag manager
	*
	* @return void
	*/
	public function action_index()
	{
		if (!isset( $_GET['state'] ))
		{
			$v = View::factory( 'ui/tpl_tag_manager' );
			View::bind_global( 'tags', $tags );
			View::bind_global( 'person', $this->person );
		
			$this->template->subtpl_main = $v;
		}
		else
		{
			$v = View::factory( 'ui/tpl_tag_manager' );
			
			$tags = ORM::factory( 'tag' )->find_all();
			View::bind_global( 'tags', $tags );
			View::bind_global( 'person', $this->person );
			echo $v;
			exit;
		}
	}
	
}

?>
