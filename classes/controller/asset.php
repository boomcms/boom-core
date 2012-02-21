<?php defined('SYSPATH') or die('No direct script access.');

/**
* Asset controller.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Asset extends Kohana_Controller
{
	private $asset;
	
	public function before()
	{
		$id = $this->request->param( 'id' );
		
		$this->asset = ORM::factory( 'asset', $id );
		if (!$this->asset->loaded())
		{
			$this->request->redirect( '/' );
		}
	}
	
	/**
	* Search index method.
	* Performs all searching etc.
	*
	*/
	public function action_view()
	{

		if ($this->asset->loaded() && $this->asset->status == Model_Asset::STATUS_PUBLISHED)
		{
			$this->asset = Asset::factory( $this->asset->type, $this->asset );	
			$this->asset->show( $this->request->param( 'width' ), $this->request->param( 'height' ));
		}
	}
	
	public function action_save()
	{
		$this->asset->title = Arr::get( $_POST, 'title' );
		$this->asset->filename = Arr::get( $_POST, 'filename' );
		$this->asset->description = Arr::get( $_POST, 'description' );
		$this->asset->status = Arr::get( $_POST, 'status' );
		$this->asset->visible_from = strtotime( Arr::get( $_POST, 'visible_from' ) );
		$this->asset->save();
		
		$this->request->redirect( '/cms/assets/view/' . $this->asset->id );
	}
}

?>
