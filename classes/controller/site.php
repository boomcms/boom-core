<?php defined('SYSPATH') or die('No direct script access.');

/**
* Sledge site controller.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Site extends Sledge_Controller
{
	/**
	* Set the default template.
	* Used by Controller_Template to know which template to use.
	* @see http://kohanaframework.org/3.0/guide/kohana/tutorials/hello-world#that-was-good-but-we-can-do-better
	* @access public
	* @var string
	*/
	public $template = 'site/standard_template';
	
	/**
	* The requested page.
	* @access protected
	* @var object
	*/
	protected $page;
	
	protected $_params = array();
	
	public function before()
	{
		parent::before();
		
		// All pages and templates are hard coded for the CMS so this is all site specific.
		// Find the requested page.
		// If /ajax is in the uri then $ajax will be > 0. This will be used later to decide whether display the template in an iframe of the standard template.
		$uri = Request::detect_uri();	
		$uri = str_replace( '/ajax', '', $uri, $ajax );

		if ($uri != '')
		{
			$uri = substr( $uri, 1 );
		}
		
		/* If the URI is more than 1 level deep search for a matching URI and put any extra levels in an array of parameters.
		 For example,
			uri: blog/author/Nick%20Smith
			Will first try and find a page with 'blog/author/Nick%20Smith' as a uri.
			If this fails 'blog/author' will be used as the uri.
			If this fails 'blog' will be used.
			If a page is found with a uri of 'blog' this will be used for the page.
			$this->_params will contain array( 'author', 'Nick Smith' );
			
			This is only done when the URI is more than 1 level deep, otherwise there's no paramaters included anyway!
		*/
		$parts = explode( '/', $uri );
		$count = count( $parts );
		
		if ($count < 2)
		{
			$page_uri = ORM::factory( 'page_uri' )->where( 'uri', '=', $uri )->find();
		}
		else
		{
			for ($i = $count; $i >= 0; $i--)
			{
				$uri = implode( '/', array_slice( $parts, 0, $i ) );
				$page_uri = ORM::factory( 'page_uri' )->where( 'uri', '=', $uri )->find();
				
				if ($page_uri->loaded())
				{
					$this->_params = array_slice( $parts, $i );
					break;
				}			
			}
		}
				
		// If the page wasn't found by URI load the 404 page.
		if ((!$page_uri || !$page_uri->loaded()) && $uri != 'error/404')
			throw new HTTP_Exception_404;
		
		$this->page = $page_uri->page;
		
		$this->mode = ($this->person->can( 'edit', $this->page ))? 'cms' : 'site';
		
		// If they can't edit the page check that it's visible.
		if ($this->mode == 'site')
		{
			if (!$this->page->is_visible() || !$this->page->is_published())
			{
				throw new HTTP_Exception_404;
			}
		}
		
		if (Arr::get( $_GET, 'version' ) && $this->mode == 'cms')
		{
			$page->version->clear();
			$page->version->where( 'id', '=', Arr::get( $_GET, 'version' ) )->find();
		}
		
		// Set the base template.
		if (Auth::instance()->logged_in() && !$ajax)
		{
			$this->template = View::factory( 'site/standard_template_editable' );
		}
		else
		{
			$this->template = View::factory( 'site/standard_template' );
		}

		// Add the main subtemplate to the standard template.
		$this->template->subtpl_main = View::factory( $this->page->template->filename );
	}
	
	/**
	* Show a site page
	*/
	public function action_show()
	{
		/*
		* Do some form handling stuff.
		* Ultimately this should go as a different controller method using routing rules (with a callback function)
		* to route forms to a the different controller.
		*
		* This way will do for now though. But don't forget to change me!
		*
		* This is a way of ensuring that forms, such as the contact us page, will work whichever URL they use.
		* We could conceivably create a new page with the contact us form template and it would still work.
		*
		*/
		if (Request::initial()->method() == 'POST')
		{
			$postbox = Arr::get( $_POST, 'postbox' );
			$controller = new ReflectionClass( "Controller_Form" );

			if ($controller->hasMethod( "action_$postbox" ))
			{
				$errors = Request::factory( "form/$postbox" )
						->post( array_keys( $_POST ), array_values( $_POST ) )
						->execute()->body();
					
				if ($errors != "")
				{
					$this->template->subtpl_main->errors = unserialize( $errors );
				}
				else
				{
					$this->template->subtpl_main->success = true;
				}
			}
		}
	}
	
	public function after()
	{	
		View::bind_global( 'page', $this->page );
		View::bind_global( 'params', $this->_params );
		
		parent::after();
	}
}

?>
