<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates		www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?php
require_once('hoopbasemodel.php');
class Hoopbasepagemodel extends Hoopbasemodel {
	public function absolute_uri($uri=false) {
		switch ($this->table) {
			case 'site_page':
			case 'cms_page':
			case 'page_v':
				return URI::absolute_uri($this->uri.$uri);
			case 'linkset_links_v':
				$page = O::f('site_page',$this->target_page_rid);
				return URI::absolute_uri($page->uri);
		}
	}

	public function absolute_uri_req() {
		switch ($this->table) {
			case 'site_page':
			case 'cms_page':
			case 'page_v':
				return URI::absolute_uri_req($this->uri);
			case 'linkset_links_v':
				$page = O::f('site_page',$this->target_page_rid);
				return URI::absolute_uri_req($page->uri);
		} 
	}		

	public function has_rss() {
		if($this->table != 'site_page' && $this->table != 'cms_page' && $this->table != 'page_v') return false;
		$rss_tag = O::fa('tag')->find_by_name('Has RSS');
		if (!$rss_tag->rid || !$this->rid) {return false;}
		return Tag::has_tag('page',$this->rid,$rss_tag->rid);
	}

	public function get_author(){
		return O::fa('person', $this->audit_person);
	}

	public function get_comments() {
		return Comment::get_comments($this);
	}

	public function delete_comment($comment_id=0) {
		Comment::delete_comment($this, $comment_id);
	}

	public function approve_comment($comment_id=0) {
		Comment::approve_comment($this, $comment_id);
	}

	public function get_tags($parent_tag_rid, $include_parent_tag=true, $use_find_array=false) {
		$descendant_tags = Tag::get_descendanttags($parent_tag_rid);
		if ($include_parent_tag) {
			$descendant_tags[] = $parent_tag_rid;
		}
		if (count($descendant_tags)>0) {
			$where = 'tag.id IN ('.implode(',',$descendant_tags).')';
		} else {
			$where = null;
		}
		if ($use_find_array) {
			return Relationship::find_partners('tag', $this)->where($where)->find_array();
		} else {
			return Relationship::find_partners('tag', $this)->where($where)->find_all();
		}
	}

	public function get_child_pages($parentrid=false, $model=NULL, $limit=NULL, $offset=NULL, $order=NULL) {
		$rc = RequestCache::Instance();
		$ki = Kohana::Instance();

		if ($parentrid === false) {
			$parentrid = $this->rid;
		}

		if (is_object($model)) {
			$model = (get_class($model) == 'cms_page_Model') ? 'cms_page' : 'site_page';
		} else if (!is_string($model)) {
			$model = $ki->page_model;
		}

		if (!isset($rc->child_page_cache)) {
			$rc->child_page_cache = array();
		}

		if (isset($rc->child_page_cache[$parentrid][$model][$limit][$offset][$order])) {
			return $rc->child_page_cache[$parentrid][$model][$limit][$offset][$order];
		}

		$old_model = ($model === null) ? null : is_object($model) ? get_class($model) : $model;

		if ($model == NULL) { 
			$model = O::f('site_page'); 
		} else {
			$model = O::f($model);
		}

		if ($parentrid == '' || $parentrid == 0) {
			if ($limit !== NULL) {
				$rc->child_page_cache[$parentrid][$old_model][$limit][$offset][$order] = $model->limit($limit)->find_all_by_parent_rid(NULL);
				return $rc->child_page_cache[$parentrid][$old_model][$limit][$offset][$order];
			} else {
				$rc->child_page_cache[$parentrid][$old_model][$limit][$offset][$order] = $model->find_all_by_parent_rid(NULL);
				return $rc->child_page_cache[$parentrid][$old_model][$limit][$offset][$order];
			}
		} else {
			
			$m = $model->find_by_rid($parentrid);
			if ($order==null) {
				$seq = array("sequence", "sequence", "title", "visiblefrom_timestamp");		// no seqs, please - we're british
				$order = $seq[(int)$m->child_ordering_policy_rid];
			}
			$dir = ($order == 'visiblefrom_timestamp') ? 'DESC' : 'ASC';
			if ($limit !== NULL) {
				$offset = (int) $offset;
				$limit = (int) $limit;
				$rc->child_page_cache[$parentrid][$old_model][$limit][$offset][$order] = $model->orderby($order, $dir)->limit($limit, $offset)->find_all_by_parent_rid($parentrid);
				return $rc->child_page_cache[$parentrid][$old_model][$limit][$offset][$order];
			} else {
				$rc->child_page_cache[$parentrid][$old_model][$limit][$offset][$order] = $model->orderby($order, $dir)->find_all_by_parent_rid($parentrid);
				return $rc->child_page_cache[$parentrid][$old_model][$limit][$offset][$order];
			}
		}
	}

	public function get_parent_from_level($level) {
		$ancestors = array();
		foreach ($this->get_ancestor_pages() as $ancestor) {
			$ancestors[] = $ancestor;
		}
		$ancestors[] = $this->rid;
		return $ancestors[$level-2];
	}

	public function get_hidden_from_leftnav($of = false) {
		if ($of == false) $of = $this;
		return ($of->visible_in_leftnav != 't');
	}

	public function get_hidden_from_leftnav_cms($of = false) {
		if ($of == false) $of = $this;
		return ($of->visible_in_leftnav_cms != 't');
	}

	public function get_first_ancestor_page() {
		$myparent = $this;
		$lastid = ($myparent->rid) ? $myparent->rid : $myparent->id;
		if ($myparent->parent_rid == 0) {
			return 0;
		}
		for ($i = 0;$i<=200;$i++) {
			if ($myparent->parent_rid != '') {
				$myparent = O::f($this->table)->find_by_rid($myparent->parent_rid);
			}
			if ($myparent->id == 1) {
				return $lastid;
			} else {
				$lastid = $myparent->id;
			}
		}
	}

	public function get_orderby_field($rid=false){
		$seq = array(1=>"sequence", "title", "audit_time");
		if ($rid) {
			$page = O::f($this->table, $rid);
			return $req[(int)$page->child_ordering_policy_rid];
		}
		return $seq[(int)$this->child_ordering_policy_rid];
	}

	public function get_all_from_homepage($level=1) {
		$homepage = $this->get_homepage();
		$find_by_parent_sql = $this->rid ? 'parent_rid = '.$this->get_parent_from_level($level+1).' and ' : '';
		return O::f($this->table)->where("$find_by_parent_sql mptt_left BETWEEN ".($homepage->mptt_left+1)." AND $homepage->mptt_right AND depth = ".($homepage->depth+$level))->orderby("mptt_left")->find_all();
	}

	public function check_ssl($page_obj=false) {
		$ki = Kohana::Instance();
		if (!$page_obj) { $page_obj = $this; }	
		// Check for ssl_only flag
		if (($page_obj->ssl_only == 't' || Kohana::config('core.force_ssl')) && !isset($_SERVER['HTTPS'])) {
			header('Location: https://'.@$_SERVER['HTTP_HOST'].@$_SERVER['REQUEST_URI']);
			die();
		}/* else if($page_obj->ssl_only == 'f' && !Kohana::config('core.force_ssl') && isset($_SERVER['HTTPS'])) {
			if ($rc->person->emailaddress == 'guest@hoopassociates.co.uk') {
				header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				die();
			}
		}*/
	}

	public function get_inherited_cache_duration($page_obj=false) {
		if (!$page_obj) { $page_obj = $this; }	

		if ($page_obj->cache_duration!==null) return $page_obj->cache_duration;

		$ancestor_pages = $page_obj->get_ancestor_pages(false,false);
		if (!count($ancestor_pages)) {
			return Kohana::config('cache.default_pages');
		}
		$cache_durations = array();
		foreach ($ancestor_pages as $page_v) {
			$cache_durations[$page_v->rid] = $page_v->cache_duration;
		}

		foreach ($ancestor_pages as $page) {
			if (isset($cache_durations[$page->rid]) && $cache_durations[$page->rid]!==null) {
				return $cache_durations[$page->rid];
			}
		}
		return Kohana::config('cache.default_pages');
	}

	private function recurse_tree($toplevel, $page=false) {
		if (!$page) $page = $this;
		if ($page->parent_rid == null) return 'root';
		if ($page->parent_rid == $toplevel->rid) {
			return strtolower(preg_replace('/ /','',$page->title));
		}
		$parent = O::f('site_page',$page->parent_rid);
		if ($parent->parent_rid == $toplevel->rid) {
			return strtolower(preg_replace('/ /','',$parent->title));
		}
		if ($parent->parent_rid == null) return false;
		return $this->recurse_tree($toplevel, $parent);
	}

	public function get_tree() {
		$toplevel = O::fa('page')->find_by_parent_rid_and_title(null, 'Sledge');
		return $this->recurse_tree($toplevel);
	}

	/** MPTT Methods **/

	/* Get a chunk of tree from this page and down */
	public function get_descendants($include_this_page=true, $show_all=false, $dont_use_view=false, $method=false) {
		$model = (get_class($this)=='Cms_page_Model') ? 'cms_page' : 'site_page';
		$visible_field = ($model == 'site_page') ? 'visible_in_leftnav' : 'visible_in_leftnav_cms';
		$left_start_from = ($include_this_page) ? $this->mptt_left : $this->mptt_left+1;
		$method = ($method ? $method : 'find_all');

		if ($dont_use_view) {
			if ($show_all) {
				return O::fa('page')->where("mptt_left BETWEEN $left_start_from AND $this->mptt_right")->orderby("mptt_left")->select("page_v.*, page.ref_page_status_rid")->$method();
			} else {
				return O::fa('page')->where("mptt_left BETWEEN $left_start_from AND $this->mptt_right AND $visible_field != 'f'")->orderby("mptt_left")->select("page_v.*, page.ref_page_status_rid")->$method();
			}
		} else {
			if ($show_all) {
				return O::f($model)->where("mptt_left BETWEEN $left_start_from AND $this->mptt_right")->orderby("mptt_left")->$method();
			} else {
				return O::f($model)->where("mptt_left BETWEEN $left_start_from AND $this->mptt_right AND $visible_field != 'f'")->orderby("mptt_left")->$method();
			}
		}
	}

	/* Get ancestors of this page, left is lower, right is higher */
	public function get_ancestor_pages($page=false, $descending=false, $min_depth=false, $include_this_page=false, $page_model=false, $hide_invisible=false, $skip_levels=false) {
		if (!$page) $page = $this;
		$order = ($descending) ? "desc" : "asc";
		$where = ($include_this_page) ? "mptt_left <= ".$page->current_version->mptt_left." AND mptt_right >= ".$page->current_version->mptt_right : "mptt_left < ".$page->current_version->mptt_left." AND mptt_right > ".$page->current_version->mptt_right;

		$where .= " and depth <= ".$page->current_version->depth;

		if ($skip_levels) {
			$where .= " and depth >= $skip_levels";
		}

		if (!$page_model) {
			$model = (get_class($page)=='Cms_page_Model') ? 'cms_page' : 'site_page';
			$visible_field = ($model == 'site_page') ? 'visible_in_leftnav' : 'visible_in_leftnav_cms';

			if ($hide_invisible) $where .= " and $visible_field = 't'";

			$model = O::f('page')->where($where);
		} else {
			$model = O::f('page')->where($where);
		}

		if ($min_depth) $model->where("depth >= $min_depth");
		return $model->orderby("current_version.mptt_left",$order)->find_all();
	}

	public function get_homepage() {
		$model = (get_class($this)=='Cms_page_Model') ? 'cms_page' : 'site_page';
		return ORM::factory($model)->where("current_version.uri = ''")->orderby("mptt_left")->find();
	}

	# Returns the curry draft campaign, if any
	public function getMailChimpCampaign() {
		$stuff_tag = Tag::find_or_create_tag(1,'Stuff');
		$campaign_stuff = Tag::find_or_create_tag($stuff_tag->rid,'MailChimp Campaign');
		$campaign = Relationship::find_partners('stuff',$campaign_stuff)->join('metadata_v as m1','stuff_v.rid','m1.item_rid')->join('metadata_v as m2','stuff_v.rid','m2.item_rid')->where("m1.item_tablename='stuff' and m1.key='page_rid' and m1.value = '$this->rid' and m2.item_tablename='stuff' and m2.key='status' and m2.value='unsent'")->find();
		return ($campaign->rid) ? $campaign : false;
	}

	public function getMailChimpCampaigns() {
		$stuff_tag = Tag::find_or_create_tag(1,'Stuff');
		$campaign_stuff = Tag::find_or_create_tag($stuff_tag->rid,'MailChimp Campaign');

		$campaign_ids = array();
		$all_ids = array();
		$stuff_lookup = array();

		foreach (Relationship::find_partners('stuff',$campaign_stuff)->join('metadata_v as m1','stuff_v.rid','m1.item_rid')->join('metadata_v as m2','stuff_v.rid','m2.item_rid')->join('metadata_v as m3','stuff_v.rid','m3.item_rid')->where("m1.item_tablename='stuff' and m1.key='page_rid' and m1.value = '$this->rid' and m2.item_tablename='stuff' and m2.key='mailchimp_campaign_id' and m3.item_tablename='stuff' and m3.key='status'")->select("m2.value as campaign_id, m3.value as status, stuff_v.rid as stuff_rid")->find_all() as $campaign) {
			if (!isset($campaign_ids[$campaign->status])) {
				$campaign_ids[$campaign->status] = array();
			}
			$campaign_ids[$campaign->status][] = $campaign->campaign_id;
			$all_ids[] = $campaign->campaign_id;
			$stuff_lookup[$campaign->campaign_id] = $campaign->stuff_rid;
		}

		$mc = MailChimp::Instance();

		$campaigns = array();
		$campaigns['sent'] = array();
		$campaigns['draft'] = array();
		$campaigns['schedule'] = array();
		$campaigns['save'] = array();

		foreach ($mc->getCampaigns() as $campaign) {
			if (in_array($campaign->id, $all_ids)) {
				foreach ($campaign_ids as $status => $ids) {
					if (in_array($campaign->id, $ids)) {
						$campaign_status = $status;
					}
				}

				if ($campaign->status == 'save') {
					if ($campaign->send_time) {
						# Campaign was scheduled at some point
						$campaign->status = 'canceled';
					}
				}

				# If campaign status has changed, update it in the sledge db
				if ($campaign_status != $campaign->status) {
					Metadata::set_metadata('stuff',$stuff_lookup[$campaign->id],'status',$campaign->status);
				}

				$campaigns[$campaign->status][] = $campaign;
			}
		}

		return $campaigns;
	}

	public function really_delete() {
		foreach (O::f('chunk_text_v')->find_all_by_page_vid($this->id) as $ct) {
			foreach (O::f('chunk_text_v')->find_all_by_rid($ct->rid) as $v) {
				$v->really_delete();
			}
			O::f('chunk_text',$ct->rid)->really_delete();
		}
		foreach (O::f('chunk_feature_v')->find_all_by_page_vid($this->id) as $cf) {
			foreach (O::f('chunk_feature_v')->find_all_by_rid($cf->rid) as $v) {
				$v->really_delete();
			}
			O::f('chunk_feature',$cf->rid)->really_delete();
		}
		foreach (O::f('chunk_asset_v')->find_all_by_page_vid($this->id) as $ca) {
			foreach (O::f('chunk_asset_v')->find_all_by_rid($ca->rid) as $v) {
				$v->really_delete();
			}
			O::f('chunk_asset',$ca->rid)->really_delete();
		}
		foreach (O::f('chunk_tag_v')->find_all_by_page_vid($this->id) as $ct) {
			foreach (O::f('chunk_tag_v')->find_all_by_rid($ct->rid) as $v) {
				$v->really_delete();
			}
			O::f('chunk_tag',$ct->rid)->really_delete();
		}
		foreach (O::f('chunk_linkset_v')->find_all_by_page_vid($this->id) as $cl) {
			foreach (O::f('linkset_links_v')->find_all_by_chunk_linkset_rid($cl->rid) as $ll) {
				foreach (O::f('linkset_links_v')->find_all_by_rid($ll->rid) as $v) {
					$v->really_delete();
				}
				O::f('linkset_links',$ll->rid)->really_delete();
			}
			foreach (O::f('chunk_linkset_v')->find_all_by_rid($cl->rid) as $v) {
				$v->really_delete();
			}
			O::f('chunk_linkset',$cl->rid)->really_delete();
		}

		O::q("delete from page_v where id = {$this->id}");
	}

	public function really_delete_all() {
		O::q('delete from page where id = '.$this->rid);

		foreach (O::f('page_v')->find_all_by_rid($this->rid) as $page_v) {
			foreach (O::f('chunk_text_v')->find_all_by_page_vid($page_v) as $ct) {
				foreach (O::f('chunk_text_v')->find_all_by_rid($ct->rid) as $v) {
					$v->really_delete();
				}
				O::f('chunk_text',$ct->rid)->really_delete();
			}
			foreach (O::f('chunk_feature_v')->find_all_by_page_vid($page_v) as $cf) {
				foreach (O::f('chunk_feature_v')->find_all_by_rid($cf->rid) as $v) {
					$v->really_delete();
				}
				O::f('chunk_feature',$cf->rid)->really_delete();
			}
			foreach (O::f('chunk_asset_v')->find_all_by_page_vid($page_v) as $ca) {
				foreach (O::f('chunk_asset_v')->find_all_by_rid($ca->rid) as $v) {
					$v->really_delete();
				}
				O::f('chunk_asset',$ca->rid)->really_delete();
			}
			foreach (O::f('chunk_tag_v')->find_all_by_page_vid($page_v) as $ct) {
				foreach (O::f('chunk_tag_v')->find_all_by_rid($ct->rid) as $v) {
					$v->really_delete();
				}
				O::f('chunk_tag',$ct->rid)->really_delete();
			}
			foreach (O::f('chunk_linkset_v')->find_all_by_page_vid($page_v) as $cl) {
				foreach (O::f('linkset_links_v')->find_all_by_chunk_linkset_rid($cl->rid) as $ll) {
					foreach (O::f('linkset_links_v')->find_all_by_rid($ll->rid) as $v) {
						$v->really_delete();
					}
					O::f('linkset_links',$ll->rid)->really_delete();
				}
				foreach (O::f('chunk_linkset_v')->find_all_by_rid($cl->rid) as $v) {
					$v->really_delete();
				}
				O::f('chunk_linkset',$cl->rid)->really_delete();
			}

			O::q("delete from page_v where id = {$page_v}");
		}
	}

	public function has_non_inherited_perms($person_rid, $group_rid) {
		return (O::f('permission')->find_by_person_rid_and_group_rid_and_where_table_and_where_rid_and_inherited($person_rid,$group_rid,'page',$this->rid,'f')->id);
	}

	public function delete_all_perms($person_rid, $group_rid) {
		if ($person_rid) {
			O::q("delete from permission where person_rid = $person_rid and where_table = 'page' and where_rid = $this->rid");
		} else {
			O::q("delete from permission where group_rid = $group_rid and where_table = 'page' and where_rid = $this->rid");
		}
	}

	public function delete_all_inherited_perms($person_rid, $group_rid) {
		if ($person_rid) {
			O::q("delete from permission where person_rid = $person_rid and where_table = 'page' and where_rid = $this->rid and inherited = 't'");
		} else {
			O::q("delete from permission where group_rid = $group_rid and where_table = 'page' and where_rid = $this->rid and inherited = 't'");
		}
	}

	public function delete_inherited_perms($person_rid, $group_rid, $what) {
		if ($person_rid) {
			O::q("delete from permission where person_rid = $person_rid and where_table = 'page' and where_rid = $this->rid and inherited = 't' and what = '$what'");
		} else {
			O::q("delete from permission where group_rid = $group_rid and where_table = 'page' and where_rid = $this->rid and inherited = 't' and what = '$what'");
		}
	}

	public function get_approval_process() {
		foreach ($this->get_ancestor_pages(false, true, false, true) as $page) {
			if ($page->approval_process_rid !== null) {
				return $page->approval_process_rid;
			}
		}
		return null;
	}

	public function get_approval_status() {
		$approval = $this->get_approval_process();

		if ($approval == NULL) return 4;

		if ($this->approval_process_status_rid == NULL) return 1;

		return (O::fa('approval_process',$this->approval_process_status_rid)->ref_approval_process_action_type_rid) +1;
	}

	public function can_approve() {
		$ki = Kohana::Instance();

		if (preg_match('/\.hoopassociates\.co\.uk$/',$ki->person->emailaddress)) return true;

		if ($this->approval_process_status_rid == NULL) {
			$where = 'parent_rid is null';
		} else {
			$where = 'parent_rid = '.$this->approval_process_status_rid;
		}

		$next_step = O::fa('approval_process')->where($where)->find();

		if ($next_step->not_author == 't' && $this->audit_person == $ki->person->rid) return false;

		if ($next_step->required_what && !Permissions::may_i($next_step->required_what)) return false;

		$vid = (isset($this->vid) ? $this->vid : $this->id);

		switch ($next_step->ref_approval_target_rid) {
			case 1: // Approval by anyone
				return true;
			case 2: // Approval by specific person
				return ($next_step->person_rid == $ki->person->rid);
			case 3: // Approval by specific group
				return (Relationship::find(array('person','tag'),array($ki->person->rid,$next_step->group_rid),false,true));
		}

		return false;
	}

	public function can_publish() {
		
	
		$ki = Kohana::Instance();
		
		
		if (preg_match('/\.hoopassociates\.co\.uk$/',$ki->person->emailaddress)) {
		 	return true;
		}

		if (!$this->approval_process_status_rid) {
			$next_step = O::fa('approval_process')->where('parent_rid is null')->find();
		} else {
			$next_step = O::fa('approval_process')->where('parent_rid = '.$this->approval_process_status_rid)->find();
		}

		// check for not_author flag
		
		if ($next_step->not_author == 't' && $this->audit_person == $ki->person->rid) {
			return false;
		}

		if (!Permissions::may_i('publish')) {
			
			// This is where we're coming out.
			return false;
			
		}

		switch ($next_step->ref_approval_target_rid) {
			case 1: // Anyone can publish
				return true;
			case 2: // Specific person can publish
				return ($next_step->person_rid == NULL || $next_step->person_rid == $ki->person->rid);
			case 3: // Specific group can publish
				return ($next_step->group_rid == NULL || Relationship::find(array('person','tag'),array($ki->person->rid,$next_step->group_rid),false,true));
		}

		return false;
	}

	// Create a new version of a page and make it active
	public function new_version() {
		$page_v = O::f('page_v');
		foreach ($this as $key => $value) {
			if ($key != 'id') {
				$page_v->{$key} = $value;
			}
		}

		$page_v->save_activeversion();

		$vid = isset($this->vid) ? $this->vid : $this->id;

		foreach (O::fa('chunk_text')->find_all_by_page_vid($vid) as $chunk_text) {
			$ct = O::f('chunk_text_v');
			$ct->page_vid = $page_v->id;
			$ct->slotname = $chunk_text->slotname;
			$ct->text = $chunk_text->text;
			$ct->save_activeversion();
		}

		foreach (O::fa('chunk_asset')->find_all_by_page_vid($vid) as $chunk_asset) {
			$ca = O::f('chunk_asset_v');
			$ca->page_vid = $page_v->id;
			$ca->slotname = $chunk_asset->slotname;
			$ca->asset_rid = $chunk_asset->asset_rid;
			$ca->text = $chunk_asset->text;
			$ca->save_activeversion();
		}

		foreach (O::fa('chunk_tag')->find_all_by_page_vid($vid) as $chunk_tag) {
			$ct = O::f('chunk_tag_v');
			$ct->page_vid = $page_v->id;
			$ct->slotname = $chunk_tag->slotname;
			$ct->target_tag_rid = $chunk_tag->target_tag_rid;
			$ct->save_activeversion();
		}

		foreach (O::fa('chunk_feature')->find_all_by_page_vid($vid) as $chunk_feature) {
			$cf = O::f('chunk_feature_v');
			$cf->page_vid = $page_v->id;
			$cf->slotname = $chunk_feature->slotname;
			$cf->target_page_rid = $chunk_feature->target_page_rid;
			$cf->save_activeversion();
		}

		foreach (O::fa('chunk_linkset')->find_all_by_page_vid($vid) as $chunk_linkset) {
			$cl = O::f('chunk_linkset_v');
			$cl->page_vid = $page_v->id;
			$cl->slotname = $chunk_linkset->slotname;
			$cl->title = $chunk_linkset->title;
			$cl->save_activeversion();

			foreach (O::fa('linkset_links')->find_all_by_chunk_linkset_rid($chunk_linkset->rid) as $linkset) {
				$ll = O::f('linkset_links_v');
				$ll->chunk_linkset_rid = $cl->rid;
				$ll->name = $linkset->name;
				$ll->uri = $linkset->uri;
				$ll->sequence = $linkset->sequence;
				$ll->target_page_rid = $linkset->target_page_rid;
				$ll->save_activeversion();
			}
		}

		return $page_v;
	}
	
	
	public function save_new_features($features) {
		$vid = (isset($this->vid)) ? $this->vid : $this->id;

		foreach ($features as $slotname => $target_page_rid) {
			$cf = O::fa('chunk_feature')->find_by_page_vid_and_slotname($vid, $slotname);
			if ($cf->rid) {
				if ($target_page_rid == 0) {
					$cf->delete();
				} else {
					$cf->target_page_rid = $target_page_rid;
					$cf->save();
				}
			} else {
				if ($target_page_rid != 0) {
					$cf->target_page_rid = $target_page_rid;
					$cf->slotname = $slotname;
					$cf->page_vid = $vid;
					$cf->save_activeversion();
				}
			}
		}
	}

	public function save_new_linksets($linksets) {
		$vid = (isset($this->vid)) ? $this->vid : $this->id;

		foreach ($linksets as $slotname => $linkset) {
			$cl = O::fa('chunk_linkset')->find_by_page_vid_and_slotname($vid, $slotname);
			if ($cl->rid) {
				$cl->save();
			} else {
				$cl = O::f('chunk_linkset_v');
				$cl->page_vid = $vid;
				$cl->slotname = $slotname;
				$cl->save_activeversion();
			}

			foreach (O::fa('linkset_links')->find_all_by_chunk_linkset_rid($cl->rid) as $ll) {
				$ll->delete();
			}

			foreach ($linkset->links as $link) {
				$ll = O::f('linkset_links_v');
				$ll->chunk_linkset_rid = $cl->rid;
				$ll->name = $link->name;
				$ll->uri = $link->uri;
				$ll->sequence = $link->sequence;
				$ll->target_page_rid = $link->target_page_rid;
				$ll->save_activeversion();
			}
		}
	}

	public function save_new_asset_chunks($asset_chunks) {
		$vid = (isset($this->vid)) ? $this->vid : $this->id;

		foreach ($asset_chunks as $slotname => $asset_rid) {
			$ca = O::fa('chunk_asset')->find_by_page_vid_and_slotname($vid, $slotname);
			if ($ca->rid) {
				$ca->asset_rid = $asset_rid;
				$ca->save();
			} else {
				$ca->page_vid = $vid;
				$ca->slotname = $slotname;
				$ca->asset_rid = $asset_rid;
				$ca->save_activeversion();
			}
		}
	}

	public function save_new_text_chunks($text_chunks) {
		$vid = (isset($this->vid)) ? $this->vid : $this->id;
		$defaults = Kohana::config('default_content.defaults');

		foreach ($text_chunks as $slotname => $text) {
			if (isset($defaults[$slotname])) {
				if (strip_tags($text) == $defaults[$slotname]) $text = '';
			} else {
				if (strip_tags($text) == $defaults['default']) $text = '';
			}

			$ct = O::fa('chunk_text')->find_by_page_vid_and_slotname($vid, $slotname);
			if ($ct->rid) {
				$ct->text = urldecode($text);
				$ct->save();
			} else {
				$ct->page_vid = $vid;
				$ct->slotname = $slotname;
				$ct->text = urldecode($text);
				$ct->save_activeversion();
			}
		}
	}

	public function save_new_tags($tags) {
		$guest = O::fa('person')->find_by_emailaddress('guest@hoopassociates.co.uk');

		foreach (O::f('relationship')->join('relationship_partner as r1','r1.relationship_id','relationship.id')->join('relationship_partner as r2','r2.relationship_id','relationship.id')->where("r1.item_tablename='page' and r1.item_rid={$this->rid} and r2.item_tablename='tag'")->find_all() as $rel) {

			$group_tag_partner = O::f('relationship_partner')->find_by_relationship_id_and_item_tablename_and_description($rel->id, 'tag', 'access_control');
			if ($group_tag_partner->id) {
				# delete the group permissions
				Permissions::delete('page', $this->rid, null, $group_tag_partner->item_rid, null, null);
				# delete the guest user perms
				Permissions::delete('page', $this->rid, $guest->rid, null, null, null);
			}
			# delete the relationship
			$rel->delete();
		}

		foreach ($tags as $tag_rid) {
			Relationship::create_relationship(array('page','tag'),array($this->rid,$tag_rid));
		}
	}
	
	public function savepage($data, $page) {
		$ki = Kohana::Instance();
		$this->audit_person = $ki->person->rid;

		foreach ($this->process_string_values($data, $page) as $key => $value) {
			${$key} = $value;
		}

		if (!isset($parent)) {
			$parent = ($this->parent_rid) ? O::fa('page',$this->parent_rid) : false;
		}

		if ((preg_match("/untitled(\d*)$/", $this->uri) || $this->uri == 'untitled') and strtolower($this->title) != 'untitled') {
			$this->uri = URI::title_to_uri($parent, $this);
		}

		Page::set_visible_flags($parent);

		if (!isset($ki->unit_testing)) {
			O::begin(array('page_v','page'));
		}

		$this->ref_page_v_status_rid = 4;
		$this->save();

		if ($this->parent_rid != $page->parent_rid && isset($new_sequence)) {
			$pager = O::f('page',$this->rid);
			$pager->sequence = $new_sequence;
			$pager->save();
			MPTT::build_tree_fast();
		}

		if ($this->children_hidden_from_leftnav != $page->children_hidden_from_leftnav || $this->children_hidden_from_leftnav_cms != $page->children_hidden_from_leftnav_cms) {
			Page::propagate_descendant_visibility();
		}

		# child sequences
		if ($this->child_ordering_policy_rid <2 && isset($sequences) && is_array($sequences) && count($sequences)>0) {
			Page::update_child_sequences($sequences);
		}

		# If the parent is sorting children alphabetically and our title is changed we need to update the MPTT tree
		if ($parent && $parent->child_ordering_policy_rid == 2 && $page->title != $this->title) {
			Page::reorder_child_trees($parent);
		}

		# If the child ordering policy has changed we need to update the MPTT tree
		if ($this->child_ordering_policy_rid != $page->child_ordering_policy_rid && $this->child_ordering_policy_rid >=2) {
			Page::child_ordering_policy_changed();
		}

		$this->secure();

		if (!Permissions::may_i("Delete",$this)) { $_POST['delete'] = null; }

		if (@$data->delete && $this->rid != O::f('site_page')->get_homepage()->rid) {

			Page::delete_page($page, @$descendants);

			if (isset($ki->unit_testing)) return;

			O::commit();

			$this->page_saved_events();

			$goto_page_rid = ($this->parent_rid) ? $this->parent_rid : 1;

			$this->gotopage = O::f('cms_page')->find_by_id($goto_page_rid);
			
			$response = (@$error == '' ? (isset($_SERVER['HTTPS']) ? "https://" . $_SERVER['SERVER_NAME'] . "/" . $this->gotopage->uri : "http://" . $_SERVER['SERVER_NAME'] . "/" . $this->gotopage->uri) : $error);
		} else if (@$_POST['delete'] != 1) {
			$this->activity_log('page edited');

			$response = (@$error == '' ? ((isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['SERVER_NAME'] . "/" . $this->uri) : $error);

			Page::create_tsvector();

			if (isset($ki->unit_testing)) return;

			O::commit();

			$this->page_saved_events();
		}

		return $response;
	}
	
	public function process_string_values($data, $page) {
		$return = array();

		foreach ($data as $key => $value) {
			if (is_string($value)) {

				$value = urldecode($value);

				switch ($key) {
					case 'parent_rid':
						foreach (Page::new_parent_rid($value, $page) as $name => $res) {
							$return[$name] = $res;
						}
						break;
					case 'visiblefrom_timestamp':
						$this->visiblefrom_timestamp = date::timezone_convert($value);
						break;
					case 'default_child_uri_prefix':
						$this->default_child_uri_prefix = preg_replace("/(^\/)|(\/$)/", '', $value);
						break;
					case 'template':
						Page::template_changed($value, $page);
						break;
					case 'visible_to_timestamp':
						if ($r = Page::visible_to_timestamp_changed($data, $value)) {
							$return['error'] = $r;
						}
						break;
					case 'uri':
						Page::uri_changed($value, $page);
						break;
					default:
						if ($value != '' && $value != 'NULL') {
							$this->{$key} = $value;
						}
				}
			}
		}

		return $return;
	}
	
	# sets the read perms for the given group tag rid, and sets the read perms for guest user
	public function secure($group_tag_rid=0){

		# get person groups
		$groups_tag = Tag::find_tag(1, 'Groups');
		$groups_groups_tag = Tag::find_tag($groups_tag->rid, 'Groups');

		# get 'read' what permission tag
		$permissions_tag = Tag::find_tag(1, 'Permissions');
		$permissions_whats_tag = Tag::find_tag($permissions_tag->rid, 'Whats');
		$permissions_whats_read_tag = Tag::find_tag($permissions_whats_tag->rid, 'Read');

		$found_groups_tag = false;
		foreach($partners = Relationship::find_partners('tag', $this)->find_all() as $tag) {
			# is this a person group tag?
			if ($tag->rid != $groups_groups_tag->rid and in_array($groups_groups_tag->rid, $tag->get_ancestortags())) {
				# find the relationship partner rows and mark them as 'access control'
				foreach($rels = O::f('relationship_partner')->find_all_by_relationship_id($tag->relationship_id) as $relationship_partner) {
					$relationship_partner->description = 'access_control';
					$relationship_partner->save();
				}
				# this groups now has read perms for this page
				Permissions::overwrite_permissions('page', $this->rid, 0, $tag->rid, array('cant' => array(), 'can' => array($permissions_whats_read_tag->rid)), 0, 0, $this->tree);
				$found_groups_tag = true;
			}
		}
		# set the perms for the guest user
		if ($found_groups_tag) {
			$guest = O::fa('person')->find_by_emailaddress('guest@hoopassociates.co.uk');
			Permissions::overwrite_permissions('page', $this->rid, $guest->rid, 0, array('cant' => array($permissions_whats_read_tag->rid), 'can' => array()), 0, 0, $this->tree);
		}
	}
	
	public function activity_log($activity, $title=false) {
		$v = O::f('activitylog_v');
		$v->remotehost = @$_SERVER['REMOTE_ADDR'];
		$v->activity = $activity;
		if ($title) $v->note = $title;
		else $v->note = $this->title;
		$v->save_activeversion();
	}
	
	public function page_saved_events($page_rid=false) {
		if (!$page_rid) $page_rid = $this->rid;
		Event::run('page.saved');
		Event::run('features.changed');
		Event::run('asset_slots.changed');
		Event::run('linkset_slots.changed');
		Event::run('text_slots.changed',$page_rid);
	}
	
}
?>
