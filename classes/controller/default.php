<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Default extends Controller_Template {

	public function action_index()	{
		echo  $this->template;
	}

}

?>
