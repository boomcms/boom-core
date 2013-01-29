<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * CMS chunk controller.
 * This controller doesn't handle any editing itself - it displays the templates which gives users the ability to edit chunks inline.
 * The chunks are saved through the page save controller.
 *
 * @package	BoomCMS
 * @category	Chunks
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Controller_Cms_Chunk extends Boom_Controller
{
	/**
	 *
 	 * @var	Model_Page	Object representing the current page.
	 */
	protected $page;

	/**
 	 * Load the current page.
	 * All of these methods should be called with a page ID in the params
	 * Before the methods are called we find the page so it can be used, clever eh?
	 *
	 * @return	void
	 */
	public function before()
	{
		parent::before();

		$this->page = new Model_Page($this->request->param('page_id'));
	}

	/**
	* Insert an internal link into a text slot.
	* This controller displays the form to select a page to link to.
	*/
	public function action_insert_url()
	{
		$this->template = View::factory('boom/editor/slot/insert_link');
	}
}
