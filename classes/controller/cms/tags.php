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
		$subtpl_topbar = View::factory( 'cms/ui/tags/topbar' );
		
		View::bind_global( 'subtpl_topbar', $subtpl_topbar );
	}

	
	/**.
	* Displays the tag manager
	*
	* @return void
	*/
	public function action_index()
	{
		$v = View::factory( 'cms/ui/tags/manager' );
		View::bind_global( 'tags', $tags );
		View::bind_global( 'person', $this->person );
	
		$this->template->subtpl_main = $v;
	}
	
}

?>
