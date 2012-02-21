<?php defined('SYSPATH') or die('No direct script access.');

/**
* CMS chunk controller
* Contains methods for editing a page chunk.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Cms_Chunk extends Controller_Cms
{
	/**
	* Object representing the current page.
	* 
	* @var object
	* @access private
	*/
	protected $_page;
	
	/**
	* Load the current page.
	* All of these methods should be called with a page ID in the params
	* Before the methods are called we find the page so it can be used, clever eh?
	*
	* @return void
	*/	
	public function before()
	{
		parent::before();
		
		$page_id = $this->request->param( 'id' );
		$page_id = (int) preg_replace( "/[^0-9]+/", "", $page_id );
		$this->_page = ORM::factory( 'page', $page_id );
	}
	
	/**
	* Display the edit feature template.
	*/
	public function action_feature()
	{
		$v = View::factory( 'cms/ui/site/page/slot/feature' );
		$v->page = $this->_page;
		
		$this->response->body( $v );
	}
	
	/**
	* Display the edit asset template.
	*/
	public function action_asset()
	{
		$v = View::factory( 'cms/ui/site/page/slot/asset' );
		$v->page = $this->_page;
		
		$this->response->body( $v );
	}
	
	public function action_insert()
	{
		$template = Arr::get( $_GET, 'template' );
		if ($template == 'undefined')
		{
			$template = null;
		}
		
		$chunk = ORM::factory( 'chunk' );
		$chunk->type = Arr::get( $_GET, 'slottype' );
		$chunk->slotname = Arr::get( $_GET, 'slotname' );
		
		if ($chunk->type == 'feature')
		{
			$chunk->data->target = ORM::factory( 'page', Arr::get( $_GET, 'preview_target_rid' ) );
		}
		
		$output = "<div class='chunk-slot {" . $chunk->type . " " . $chunk->slotname . " " . Arr::get( $_GET, 'preview_target_rid' ) . "}>" . $chunk->show( $template ) . "</div>";
		
		$this->response->body( $output );
	}
	
	public function after()
	{
		echo $this->response;
		exit;
	}
}

?>
