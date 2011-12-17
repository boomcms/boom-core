<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Cms_Default extends Controller_Template
{
	public function action_index()
	{
		$this->template->subtpl_main = View::factory( 'cms/home' );
		echo $this->template;

	}

}

?>
