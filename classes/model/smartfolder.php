<?php

/**
* @package Tag
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
* This class will be used to represent smart folders, which are a type of tag.
* At the moment it extends tag_Model but using a decorator would probably be best.
* I haven't moved onto tag models yet though - this class has been created solely fo rthe purpose of dumping some code I found elsewhere.
*/

class smartfolder_Model extends tag_Model {
	
	/**
	*
	* Direct copy and paste, this used to be stored in the Asset library. This code hasn't yet been edited to work with new models.
	*
	*/
	public function get_smartfolder_model($tag) {
		$assets = Tag::find_or_create_tag(1,'Assets');
		$smart_folders = Tag::find_or_create_tag($assets->rid,'Smart folders',false,false,true);
		$uploaded_by = Tag::find_or_create_tag($smart_folders->rid, 'Uploaded by');

		if ($tag->parent_rid == $uploaded_by->rid) {
			$ex = explode(' ',$tag->name);
			$person = O::fa('person')->find_by_firstname_and_lastname($ex[0],$ex[1]);
			if ($person->rid) {
				return O::fa('asset')->where("asset_v.audit_person ='$person->rid'");
			}
		}

		switch ($tag->name) {
			case 'All assets':
			case 'Filesize':
			case 'Type':
			case 'Uploaded by':
				return O::fa('asset');
			case 'Rubbish':
				return O::fa('asset')->where("asset.deleted is false");
			case 'Document':
				return O::fa('asset')->join('asset_type_v','asset_type_v.rid','asset_v.asset_type_rid')->join('asset_type','asset_type.active_vid','asset_type_v.id')->where("asset_type_v.name = 'pdf'");
			case 'Image':
				return O::fa('asset')->join('asset_type_v','asset_type_v.rid','asset_v.asset_type_rid')->join('asset_type','asset_type.active_vid','asset_type_v.id')->where("asset_type_v.name IN ('gif','jpeg','png')");
			case 'GIF':
				return O::fa('asset')->join('asset_type_v','asset_type_v.rid','asset_v.asset_type_rid')->join('asset_type','asset_type.active_vid','asset_type_v.id')->where("asset_type_v.name = 'gif'");
			case 'JPG':
				return O::fa('asset')->join('asset_type_v','asset_type_v.rid','asset_v.asset_type_rid')->join('asset_type','asset_type.active_vid','asset_type_v.id')->where("asset_type_v.name = 'jpeg'");
			case 'PNG':
				return O::fa('asset')->join('asset_type_v','asset_type_v.rid','asset_v.asset_type_rid')->join('asset_type','asset_type.active_vid','asset_type_v.id')->where("asset_type_v.name = 'png'");
			case 'Large':
				return O::fa('asset')->where("filesize >1048576");
			case 'Medium':
				return O::fa('asset')->where("filesize >102400 and filesize <= 1048576");
			case 'Small':
				return O::fa('asset')->where("filesize <= 102400");
		}

		return O::fa('asset');
	}	
	
	
}