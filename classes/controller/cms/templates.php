<?php

/**
* Controller for doing stuff with templates.
*
* @package Sledge
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates Ltd
*/
class Controller_Cms_Templates extends Controller_Template
{
	
	/**
	* Add a new template to the database.
	* Code mostly copied from template.
	*
 	*/
	public function action_add()
	{
		$template = ORM::factory('template');
		$template->version->name = $_POST['name'];
		$template->version->description = $_POST['description'];
		$template->version->filename = $_POST['filename'];
		$template->version->visible = $_POST['visible'];
		$template->save();
	}
	
	/**
	* Edit an existing template.
	* @todo tidy up the code.
	*/
	public function action_edit()
	{
		$template = ORM::factory( 'template', $_POST['id'] );
		$template->version->name = $_POST['name'];
		$template->version->description = $_POST['description'];
		$template->version->filename = $_POST['filename'];
		$template->version->visible = $_POST['visible'];
		$template->save();
	}
	
	/**
	* Display the list of available templates.
	*
	*/
	public function action_index()
	{
		
		$templates = ORM::factory( 'template' )->where( 'visible', '=', true )->order_by( 'name' )->find_all();
		
		$this->template->subtpl_main = View::factory( 'cms/templates/tpl_templates' );
		$this->template->subtpl_main->templates = $templates;	
		
		echo $this->template;	
	}
	
	/**
	* Scans the templates directory to find new templates and adds them to the database.
	* Code copied from the old templates cms template.
	*
	* @todo make this code nice.
	* @return array Array of added templates.
	*/
	private function find()
	{
		if ($dh = @opendir (APPPATH."views/site/templates"))
		{
			while ($file = readdir($dh))
			{
				if (preg_match('/\.php$/',$file))
				{
					$name = preg_replace( '/\.php$/','' , $file );
					
					$template = ORM::factory('template')->find_by_filename( 'site/templates/' . $name );
					if (!$template->id)
					{
						$template->version->name = $name;
						$template->version->filename = 'site/templates/' . $name;
						$template->save();
					}
				}
			}
			closedir($dh);
		}
	}
}

?>