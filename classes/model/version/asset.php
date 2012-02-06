<?php

/**
* The version table for assets.
*
* Table name: asset_v
* 
*************************** Table Columns ************************
****	Name			****	Data Type	****	Description		
****	id				****	integer		****	Primary key. auto increment.			
****	rid				****	integer		****	ID of the asset this version belongs to.
****	title			****	string		****	Title of the asset.
****	description		****	string		****	A description of the asset displayed in the asset manager.
****	width			****	integer		****	Not sure what this is for.
****	height			****	integer		****	Ditto.
****	filename		****	string		****	The name of the file when it was uploaded. Used to set the filename for asset downloads.
****	crop_start_x	****	integer		****	Not sure.
****	crop_start_y	****	integer		****	Ditto.
****	crop_end_x		****	integer		****	Ditto.
****	crop_end_y		****	integer		****	Ditto.
****	visible_from	****	integer		****	Unix timestamp to set a time the asset becomes visible. Not sure there's any way of changing this.
****	status			****	integer		****	Not sure.
****	synced			****	boolean		****	Ditto.
****	search_priority	****	integer		****	Urgh :/
****	type			****	string		****	The type of asset. Used for Asset::factory().
****	audit_person	****	integer		****	Person ID of the user who created the version.
****	audit_time		****	integer		****	Unix timestamp of when the version was created.
****	deleted			****	boolean		****	Whether the asset has been deleted.
****	filesize		****	string		****	The size of the uploaded file. Not being used at the moment.
******************************************************************
*
* @package Asset
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Version_Asset extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'asset_v';	
	protected $_has_one = array(
		'asset'	=> array( 'model' => 'asset', 'foreign_key' => 'id' ),
	);
	protected $_belongs_to = array( 
		'person'			=> array( 'model' => 'person', 'foreign_key' => 'id' ),
	);


}
?>
