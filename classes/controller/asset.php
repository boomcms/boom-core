<?php defined('SYSPATH') or die('No direct script access.');

/**
* Asset controller.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Asset extends Kohana_Controller
{
	/**
	* Search index method.
	* Performs all searching etc.
	*
	*/
	public function action_index()
	{
		$id = $this->request->param( 'id' );
		
		$asset = ORM::factory( 'asset', $id );
		if ($asset->loaded())
		{
			$asset = Asset::factory( $asset->type, $asset );
		
			echo $asset->show();
		}
		exit();
	}

}

?>
