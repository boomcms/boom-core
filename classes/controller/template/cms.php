<?php defined('SYSPATH') or die('No direct script access.');

/**
* Sledge CMS controller template.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Template_Cms extends Controller_Template
{
	public $auto_render = false;
	/**
	* Set the default template.
	* Used by Controller_Template to know which template to use.
	* @see http://kohanaframework.org/3.0/guide/kohana/tutorials/hello-world#that-was-good-but-we-can-do-better
	* @access public
	* @var string
	*/
	public $template = 'cms/standard_template';
	
	public function before()
	{
		parent::before();
		
		// Add the header subtemplate.
		$this->template->title = 'CMS';
		$this->template->subtpl_header = View::factory( 'site/subtpl_header' );
		$this->template->client = Kohana::$config->load( 'core' )->get( 'clientname' );
	}
	
	public function after()
	{
		echo $this->template;
	}
}

?>