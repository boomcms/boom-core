<?php

/**
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* 
*/
class Model_Version_Person extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'person_v';
	protected $_belongs_to = array( 'person' => array( 'foreign_key' => 'active_vid' ) );
	
	
	public function get_image($width=180, $height=200, $quality=85, $crop=true, $dont_exit=false) { # get_user
		$profilepic = O::fa('asset')->join('relationship_partner as r1','r1.item_rid','asset_v.rid')->join('relationship_partner as r2','r1.relationship_id','r2.relationship_id')->where("r1.item_tablename='asset' and r1.description='profilepic' and r2.item_tablename='person' and r2.item_rid=$this->rid")->find();
		if ($profilepic->rid) {
			$basepath = Kohana::config('core.assetpath');
			$filepath = $profilepic->get_filename();
			$mimetype = "image/jpg";
		} else {
			$basepath = SLEDGEPATH.'docroots/site/img/cms/';
			$filepath = "nopersonpic.png";
			$mimetype = "image/png";
		}
		list($file, $ext) = explode('.', $filepath);
		list($main_mimetype, $sub_mimetype) = explode('/', $mimetype);
		if ($sub_mimetype == 'pdf') {
			$ext = 'png';
			$mimetype = 'image/png';
		}
		$cropped = $crop ? '_crop' : '';
		$cachefile = Kohana::config('core.assetpath').'cache/'.$file.'_'.$width.'_'.$height.'_'.$quality.$cropped.'.'.$ext;
		if (!@file_exists($cachefile)) {
			if ($main_mimetype == 'image') {
				$image = new Image($basepath.$filepath);
				$imagesize = getimagesize($basepath.$filepath);
				if ($imagesize[0] > $imagesize[1] and $imagesize[0] > $width) {
					$image->resize($width, $height, Image::HEIGHT);
				} else if ($imagesize[1] > $height) {
					$image->resize($width, $height, Image::WIDTH);
				}
				if ($crop) {
					$image->crop($width, $height);
				}
				$image->quality($quality);
				$image->save($cachefile);
			} elseif ($main_mimetype == 'text') {
				$file = SLEDGEPATH . '/docroots/site/img/icons/40x40/txt_icon.gif';
			} elseif ($main_mimetype == 'application') {
				if ($sub_mimetype == 'pdf') {
					exec('convert -scale '.$width.'x'.$height.' '. $basepath . $filepath . '[0] ' . $cachefile);
				}
			}
		}

		header('Content-type: '.$mimetype);
		header("Content-Length: ".filesize($cachefile));
		ob_clean(); flush(); readfile($cachefile);

		if (!$dont_exit) exit;
	}

	public function get_groups_highest() { # get_user_toplevel_groups # get_toplevel_groups
		$toplevel_tag = Tag::find_or_create_tag(1, 'Groups');
		$toplevel_tag = Tag::find_or_create_tag($toplevel_tag->rid, 'Groups');
		return Relationship::find_partners('tag','person',$this->rid)->where("tag_v.parent_rid = {$toplevel_tag->rid}")->find_all();
	}

	# This method is a lie, there can be two groups at the same level and there's no way to say which is the primary.
	# We are currently ignoring this issue.
	public function get_primary_group() {
		$toplevel_tag = Tag::find_or_create_tag(1, 'Groups');
		$toplevel_tag = Tag::find_or_create_tag($toplevel_tag->rid, 'Groups');
		$descendant_tags = Tag::get_descendanttags($toplevel_tag->rid);
		foreach($groups = Relationship::find_partners('tag','person',$this->rid)->orderby('tag_v.rid')->find_all() as $group) {
			// if this tag is a descendant group tag
			if (in_array($group->rid, $descendant_tags)) {
				return $group;
			}
		}
	}

	public function get_primary_group_member_rids() {
		$person_rids = array();
		foreach (Relationship::find_partners('person',$this->get_primary_group())->orderby('person_v.rid')->find_all() as $person_v) {
			if (!in_array($person_v->rid,$person_rids)) $person_rids[] = $person_v->rid;
		}
		return $person_rids;
	}

	public function get_groups_lowest() { # get_user_lowest_groups # get_lowest_groups
		$user_groups = array();

		$groups_tag = Tag::find_or_create_tag(1,'Groups');
		$groups_tag2 = Tag::find_or_create_tag($groups_tag->rid,'Groups');

		$groups = Tag::get_descendanttags($groups_tag2->rid);

		foreach (Relationship::find_partners('tag', 'person', $this->rid)->orderby('tag_v.rid')->find_all() as $tag_v) {
			if (in_array($tag_v->rid, $groups)) {
				$user_groups[] = $tag_v;
			}
		}

		return $user_groups;
	}

	public function get_groups() {
		return $this->get_groups_lowest();
	}

	# Returns an array of groups which the user can access
	# This is their related groups and all descendants of those groups
	public function get_accessible_groups() {
		$groups_tag = Tag::find_or_create_tag(1, 'Groups');
		$groups_tag = Tag::find_or_create_tag($groups_tag->rid, 'Groups');

		$all_possible_groups = Tag::get_descendanttags($groups_tag->rid);

		$groups = array();
		foreach (Relationship::find_partners('tag','person',$this->rid)->orderby('tag_v.rid')->find_all() as $tag_v) {
			if (in_array($tag_v->rid,$all_possible_groups)) {
				# We have a contender!
				if (!in_array($tag_v->rid,$groups)) $groups[] = $tag_v->rid;
				foreach (Tag::get_descendanttags($tag_v->rid) as $tag_rid) {
					if (!in_array($tag_rid,$groups)) $groups[] = $tag_rid;
				}
			}
		}

		return $groups;
	}

	public function get_permissions() {
		return Permissions::get_permissions($this->rid);
	}

	public function get_stack_trace_object() {
		return array_merge(array('__model__' => get_class($this)),$this->object);
	}

	public function grant($what, $where_table, $where_rid) {
		return Permissions::grant($this->rid,null,$what,$where_table,$where_rid);
	}

	public function set_permission($what, $where_table, $where_rid, $result=true) {
		return Permissions::set_permission($this->rid, null, $what, $where_table, $where_rid, $result);
	}

	public function revoke($what, $where_table, $where_rid) {
		return Permissions::revoke($this->rid,null,$what,$where_table,$where_rid);
	}

	public function send_email($subject, $msg, $headers=false) {
		mail($this->emailaddress,$subject,$msg,$headers);
	}
}
?>
