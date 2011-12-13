<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates		www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?php
require_once('hoopbasemodel.php');
class tag_v_Model extends Hoopbasemodel {
	public $ancestortags_cache;
	public function get_ancestortags($includefirstlevel=true, $returnobj=false) {
		$tid = $this->rid;
		# side order of cache
		if (isset($this->ancestortags_cache) && isset($this->ancestortags_cache[$tid][$includefirstlevel][$returnobj])) {
			return $this->ancestortags_cache[$tid][$includefirstlevel][$returnobj];
		}

		$myparent = O::fa('tag',$tid);
		if ($returnobj) {
			$ancestors = array($myparent);
		}else {
			$ancestors = array($tid);
		}
		$lastid = $myparent->rid;
		for ($i = 0;$i<=10;$i++) {
			if ($myparent->parent_rid != '') {
				$myparent = O::fa('tag',$myparent->parent_rid);
			} else {
				break;
			}
			$lastid = $myparent->rid;
			if ($myparent->rid) {
				if ($returnobj) {
					array_push($ancestors, $myparent);
				} else {
					array_push($ancestors, $myparent->rid);
				}
			}
		}
		$ancestors = array_reverse($ancestors);
		if (!$includefirstlevel) {
			unset($ancestors[0]);
		}

		if (!isset($this->ancestortags_cache)) {
			$this->ancestortags_cache = array();
		}
		$this->ancestortags_cache[$tid][$includefirstlevel][$returnobj] = $ancestors;

		return $ancestors;
	}

	public function find_or_create($name, $parent_rid) {
		$tag = $this->find_by_parent_rid_and_name($parent_rid, $name);
		if (!$tag->rid) {
			$tag = O::f('tag_v');
			$tag->parent_rid = $parent_rid;
			$tag->name = $name;
			$tag->save_activeversion();
		}
		return $tag;
	}

	public function get_mkrs() {
		return Relationship::find_partners('key_restrictions_v',$this)->where("key_restrictions_v.tablename='metadata_v'")->find_all();
	}

	public function get_permissions() {
		return Permissions::get_permissions(false,$this->rid);
	}

	public function get_children() {
		return (Tag::gettags($this->rid));
	}

	public function get_descendants_as_lookup($lower=false,$reverse=false,$lookup=false) {
		$rc = RequestCache::Instance();
		if (!$lookup) {
			if (isset($rc->tag_descendants_lookup[$this->rid][$lower][$reverse])) {
				return $rc->tag_descendants_lookup[$this->rid][$lower][$reverse];
			}
			$lookup = array();
			$first = true;
		}
		foreach (Tag::gettags($this->rid) as $tag) {
			if ($lower) $tag->name = strtolower($tag->name);
			if ($reverse) {
				$lookup[$tag->name] = $tag->rid;
			} else {
				$lookup[$tag->rid] = $tag->name;
			}
			$lookup = $tag->get_descendants_as_lookup($lower,$reverse,$lookup);
		}

		if (isset($first)) {
			if (!isset($rc->tag_descendants_lookup)) {
				$rc->tag_descendants_lookup = array();
			}
			$rc->tag_descendants_lookup[$this->rid][$lower][$reverse] = $lookup;
		}

		return $lookup;
	}

	public function get_stack_trace_object() {
		return array_merge(array('__model__' => get_class($this)),$this->object);
	}

	public function grant($what, $where_table, $where_rid) {
		return Permissions::grant(null,$this->rid,$what,$where_table,$where_rid);
	}
 
	public function set_permission($what, $where_table, $where_rid, $result=true) {
		return Permissions::set_permission(null, $this->rid, $what, $where_table, $where_rid, $result);
	}

	public function revoke($what, $where_table, $where_rid) {
		return Permissions::revoke(null,$this->rid,$what,$where_table,$where_rid);
	}

	public function has_non_inherited_perms($person_rid, $group_rid) {
		return (O::f('permission')->find_by_person_rid_and_group_rid_and_where_table_and_where_rid_and_inherited($person_rid,$group_rid,'tag',$this->rid,'f')->id);
	}

	public function delete_all_perms($person_rid, $group_rid) {
		if ($person_rid) {
			O::q("delete from permission where person_rid = $person_rid and where_table = 'tag' and where_rid = $this->rid");
		} else {
			O::q("delete from permission where group_rid = $group_rid and where_table = 'tag' and where_rid = $this->rid");
		}
	}

	public function delete_inherited_perms($person_rid, $group_rid) {
		if ($person_rid) {
			O::q("delete from permission where person_rid = $person_rid and where_table = 'tag' and where_rid = $this->rid and inherited = 't'");
		} else {
			O::q("delete from permission where group_rid = $group_rid and where_table = 'tag' and where_rid = $this->rid and inherited = 't'");
		}
	}

	public function is_smart() {
		foreach (O::fa('tag')->where("rid in (".implode(',',$this->get_ancestortags()).")")->find_all() as $anc) {
			if ($anc->name == 'Smart folders') return true;
		}
	}

	public function is_protected() {
		$system = Tag::find_or_create_tag(1,'System');
		$protected = Tag::find_or_create_tag($system->rid,'Protected');
		return Tag::has_tag('tag', $this->rid, $protected->rid);
	}
}
