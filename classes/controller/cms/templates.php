<?php

/**
* Controller for doing stuff with templates.
*
* @package Sledge
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates Ltd
*/
class Controller_Cms_Templates extends Controller_Cms
{

	public function before()
	{	
		parent::before();
		
		if (!$this->person->can( 'manage templates'  ))
			Request::factory( 'error/403' )->execute();
			
		$subtpl_topbar = View::factory( 'cms/ui/templates/topbar' );
		View::bind_global( 'subtpl_topbar', $subtpl_topbar );
		
		$this->template->title = 'Template Manager';
	}
	
	/**
	* Add a new template to the database.
	* Code mostly copied from template.
	*
 	*/
	public function action_add()
	{
		$this->template->subtpl_main = View::factory( 'cms/pages/templates/edit' );
		$this->template->subtpl_main->template = ORM::factory( 'template' );
	}
	
	/**
	* Edit an existing template.
	* @todo tidy up the code.
	*/
	public function action_edit()
	{	
		$id = $this->request->param('id' );
		$id = (int) preg_replace( "/[^0-9]+/", "", $id );
	
		$template = ORM::factory( 'template', $id );
	
		if ( $this->request->method() == 'POST')
		{
			$template->name = Arr::get( $_POST, 'name', $template->name );
			$template->description = Arr::get( $_POST, 'description', $template->description );
			$template->filename = Arr::get( $_POST, 'filename', $template->filename );
			$template->visible = (Arr::get( $_POST, 'visible' ) == 'yes')? true : false;
			$template->audit_person = $this->person->id;
			$template->save();
		
			Request::factory( '/cms/templates' )->execute();
			exit();
		}

		$this->template->subtpl_main = View::factory( 'cms/pages/templates/edit' );
		$this->template->subtpl_main->template = $template;
	}
	
	/**
	* Display the list of available templates.
	*
	*/
	public function action_index()
	{
		$new = $this->find();

		$templates = ORM::factory( 'template' )->order_by( 'name' )->find_all();
	
		$this->template->subtpl_main = View::factory( 'cms/pages/templates/index' );
		$this->template->subtpl_main->templates = $templates;	
		$this->template->subtpl_main->new = $new;
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
			$new = array();
			
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
						
						$new[] = $template;
					}
				}
			}
			closedir($dh);
			return $new;
		}
	}
}

?>
