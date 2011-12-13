<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates		www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?php
require_once('hoopbasemodel.php');
class stuff_v_Model extends Hoopbasemodel {
	public function generate_tsvector() {
		$sql = "UPDATE stuff_v SET fulltext_tsvector = ";

		foreach (O::f('metadata_v')->find_all_by_item_tablename_and_item_rid('stuff',$this->rid) as $i=>$met_v) {
			if ($i>0) $sql .= " || ";
			$sql .= "setweight(to_tsvector('pg_catalog.english', coalesce('".pg_escape_string($met_v->value)."','')),'A') ";
		}

		if (isset($i)) {
			$sql .= "WHERE rid = $this->rid";
			$this->db->query($sql);
		}
	}

	public function get_metadata() {
		$stuff_tag = Tag::find_or_create_tag(1, 'Stuff');
		$stuff_tag_rids = Tag::get_descendanttags($stuff_tag->rid);
		$metadata_tag = Tag::find_or_create_tag(1, 'Metadata');

		$tag = Relationship::find_partner('tag',$this)->where("tag_v.rid IN (".implode(',',$stuff_tag_rids).")")->find();

		$metadata_tag = Tag::find_or_create_tag($metadata_tag->rid, $tag->name);

		$metadata = array();
		foreach (array_merge(array($metadata_tag),Tag::gettags($metadata_tag->rid)) as $tag_v) {
			$fields = array();
			foreach (Relationship::find_partners('key_restrictions_v',$tag_v)->where("key_restrictions_v.tablename='metadata_v'")->find_all() as $mkr_v) {
				$fields[] = $mkr_v->key_name;
			}
			foreach (Relationship::find_partners('metadata_v',$tag_v)->where("metadata_v.item_tablename='stuff' and metadata_v.item_rid={$this->rid} and metadata_v.key IN ('".implode("','",$fields)."')")->find_all() as $met_v) {
				if (Kohana::config('core.htmlencode_metadata')) {
					$metadata[$tag_v->name][$met_v->key] = htmlspecialchars($met_v->value);
				} else {
					$metadata[$tag_v->name][$met_v->key] = $met_v->value;
				}
			}
			foreach ($fields as $field) {
				if (!isset($metadata[$tag_v->name][$field])) $metadata[$tag_v->name][$field] = null;
			}
		}

		return $metadata;
	}

	public function get_related_stuff($tag, $foreign_key, $metadata_keys=false, $sort_by_key=false) {
		$stuff = Relationship::find_partners('stuff',$tag);
		$stuff->join('metadata_v as m1','m1.item_rid','stuff_v.rid');

		if (is_array($metadata_keys)) {
			foreach ($metadata_keys as $i=>$key) {
				$stuff->join('metadata_v as m'.($i+2),'m'.($i+2).'.item_rid','stuff_v.rid');
			}
		}

		$stuff->where("m1.item_tablename='stuff' and m1.key='$foreign_key' and m1.value='{$this->rid}'");

		if (is_array($metadata_keys) && sizeof($metadata_keys)) {
			foreach ($metadata_keys as $i=>$key) {
				$stuff->where('m'.($i+2).".item_tablename='stuff' and m".($i+2).".key='$key'");
			}

			$select = "stuff_v.*";
			foreach ($metadata_keys as $i=>$key) {
				$select .= ", m".($i+2).".value as ".str_replace(' ','_',$key);
			}
			$stuff->select($select);
		}

		if ($sort_by_key) {
			$stuff->orderby(str_replace(' ','_',$sort_by_key));
		}

		$stuffs = array();
		foreach ($stuff->find_all() as $stuff_v) { $stuffs[] = $stuff_v; }

		if ($sort_by_key) {
			while (1) {
				$done = true;
				for ($i=0;$i<count($stuffs)-1;$i++) {
					if ($stuffs[$i]->$sort_by_key > $stuffs[$i+1]->$sort_by_key) {
						$tmp = $stuffs[$i];
						$stuffs[$i] = $stuffs[$i+1];
						$stuffs[$i+1] = $tmp;
						$done=false;
					}
				}
				if ($done) break;
			}
		}

		return $stuffs;
	}

	public function get_tag() {
		$stuff_tag = Tag::find_or_create_tag(1, 'Stuff');
		$stuff_tags = Tag::get_descendanttags($stuff_tag->rid);
		return Relationship::find_partner('tag',$this)->where("tag_v.rid IN (".implode(',',$stuff_tags).")")->find();
	}

	public function get_tag_name_ajax() {
		$tag = $this->get_tag();
		return preg_replace('/ /','_',strtolower($tag->name));
	}

	public function get_stack_trace_object() {
		return array_merge(array('__model__' => get_class($this)),$this->object);
	}

}
