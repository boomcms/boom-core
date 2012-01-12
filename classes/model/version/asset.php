<?php

/**
* Asset Model
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
	
	public function get_path($asset_type=false) {
		if ($asset_type) {
			return Kohana::config('core.assetpath').$this->rid.'.'.$asset_type->extension;
		}
		return Kohana::config('core.assetpath').$this->get_filename();
	}

	public function get_filename() {
		$asset_type = O::fa('asset_type',$this->asset_type_rid);

		if ($asset_type->extension) {
			return $this->rid . "." . $asset_type->extension;
		} else {
			return $this->rid . "." . strtolower(end(explode(".",$this->filename)));
		}
	}
	public function get_filename_extension() {
		return preg_replace('/(.*)\.(\w+)$/', "$2", $this->filename);
	}
	public function get_filename_noextension() {
		return preg_replace('/\.[^\.]+$/', "", $this->filename);
	}

	public function get_filesize() {
		return (($this->filesize/1024) > 1024) ? round(($this->filesize/1024)/1024, 2) . ' MB' : round(($this->filesize/1024), 2) . ' KB';
	}

	public function get_mime_mimetype() {
		return O::fa('mimetypelist')->where('mimetypelist_v.asset_type_rid = '.$this->asset_type_rid)->find()->mimetype;
	}

	public function get_mime_maintype() {
		return O::fa('asset_type', O::fa('asset_type', $this->asset_type_rid)->parent_rid)->name; 
	}

	public function get_mime_subtype() {
		return O::fa('asset_type', $this->asset_type_rid)->name;
	}

	public function get_metadata($key) {
		$value = O::f('metadata_v')->find_by_item_tablename_and_item_rid_and_key($this->table,$this->rid,$key)->value;
		if (!$value && preg_match('/_v$/',$this->table)) {
			$value = O::f('metadata_v')->find_by_item_tablename_and_item_rid_and_key(preg_replace('/_v$/','',$this->table),$this->rid,$key)->value;
		}
		return $value;
	}

	public function get_editions() {
		$product_type_rid = $this->get_metadata('product_type_rid');
		return O::fa('product_type_edition')->orderby('id')->find_all_by_product_type_rid($product_type_rid);
	}

	public function get_all_metadata() {
		if (Kohana::config('cache.asset_metadata')) {
			$cache = Cache::instance();

			if (!is_array($metadata = $cache->get('asset_metadata_'.$this->rid))) {
				unset($metadata);
			}
		}

		if (!isset($metadata)) {
			$metadata_tag = Tag::find_or_create_tag(1, 'Metadata');
			$metadata_tags = Tag::get_descendanttags($metadata_tag->rid);

			$metadata = array();

			foreach (O::f('metadata_v')->join('relationship_partner as r3','r3.item_rid','metadata_v.id')->join('relationship_partner as r4','r3.relationship_id','r4.relationship_id')->join('tag_v','tag_v.rid','r4.item_rid')->join('tag','tag.active_vid','tag_v.id')->where("metadata_v.item_tablename='asset' and metadata_v.item_rid={$this->rid} and r3.item_tablename='metadata_v' and r4.item_tablename='tag'")->select("metadata_v.*, r4.item_rid as tag_rid, tag_v.name as tag_name")->find_all() as $met_v) {
				$metadata[$met_v->tag_name][$met_v->key] = $met_v->value;
			}

			if (Kohana::config('cache.asset_metadata')) {
				$cache->set('asset_metadata_'.$this->rid,$metadata,array('asset_metadata'),0);
			}
		}

		return $metadata;
	}

	public function absolute_uri() {
		if (isset($this->uri) && $this->uri) {
			return URI::absolute_uri($this->uri);
		}

		if (isset($this->page_rid)) {
			$page = O::f('site_page',$this->page_rid);
			return URI::absolute_uri($page->uri);
		}
	}

	public function absolute_uri_req() {
		if (isset($this->uri) && $this->uri) {
			return URI::absolute_uri_req($this->uri);
		}

		if (isset($this->page_rid)) {
			$page = O::f('site_page',$this->page_rid);
			return URI::absolute_uri_req($page->uri);
		}
	}

	public function distributed_uri() {
		$val = sha1($this->uri);
		$val = hexdec($val[0]);

		$x = 0;
		for ($j=0;$j<2;$j++) {
			if ($val & (1 << $j)) {
				$x |= (1 << $j);
			}
		}

		$hostname = $_SERVER['SERVER_NAME'];
		if (preg_match('/^www\./',$hostname)) {
			$hostname = preg_replace('/^www\./','',$hostname);
		}

		return 'http'.(@$_SERVER['HTTPS'] ? 's' : '').'://img'.($x+1).'.'.$hostname.'/'.$this->uri;
	}

	public function get_stack_trace_object() {
		return array_merge(array('__model__' => get_class($this)),$this->object);
	}

	public function get_dimensions() {
		if (trim(`uname`) == 'Linux') {
                        // No need for path on Linux, ffmpeg availablility on path tested elsewhere 
                        $path = '';
		} else {
			$path = '/opt/local/bin/';
		}

		$data = shell_exec($path."ffmpeg -i ".Kohana::config('core.assetpath').$this->get_filename()." 2>&1");

		if (preg_match('/Stream #[0-9\.]+: Video: .*?, ([0-9]+)x([0-9]+),/m',$data,$m)) {
			$obj = new stdClass;
			$obj->width = $m[1];
			$obj->height = $m[2];
			return $obj;
		}

		return false;
	}

	public function encoding_exists($asset_type_rid) {
		if ($this->asset_type_rid == $asset_type_rid) return true;

		return (O::f('asset_encoding_v')->find_by_asset_rid_and_asset_type_rid($this->rid,$asset_type_rid)->id);
	}

	public function get_encoding_uri($asset_type) {
		if ($this->asset_type_rid == $asset_type->rid) {
			return '/_ajax/call/asset/get_asset/'.$this->rid.'/NULL/NULL/NULL/NULL/1/0/1/0/null.'.$asset_type->extension;
		}

		$encoding = O::f('asset_encoding_v')->find_by_asset_type_rid_and_asset_rid($asset_type->rid,$this->rid);
		if ($encoding->id) {
			return '/_ajax/call/asset/get_asset/'.$this->rid.'/NULL/NULL/NULL/NULL/1/0/1/'.$asset_type->name.'/null.'.$asset_type->extension;
		}
	}

	public function encoding_finished() {
		$video = O::fa('asset_type')->find_by_name('video');
		$h264_type = O::fa('asset_type')->find_by_parent_rid_and_name($video->rid,'mp4');
		$flv_type = O::fa('asset_type')->find_by_parent_rid_and_name($video->rid,'x-flv');
		$vp3_type = O::fa('asset_type')->find_by_parent_rid_and_name($video->rid,'ogg');
		$vp8_type = O::fa('asset_type')->find_by_parent_rid_and_name($video->rid,'webm');

		foreach (Kohana::config('encoder',false,false) as $key => $value) {
			if (preg_match('/^encode_video_([a-z0-9]+)$/',$key,$m) && $value === true) {
				$type_var = $m[1].'_type';

				if (!$this->encoding_exists(${$type_var}->rid)) {
					return false;
				}
			}
		}

		return true;
	}

	public function get_pages_that_use_me() {
		$pages = array();
		$page_rids = array();

		foreach (O::fa('chunk_text')->join('page_v','page_v.id','chunk_text_v.page_vid')->join('page','page.active_vid','page_v.id')->where("text ilike '%hoopdb://asset/".$this->rid."/%'")->select("page_v.title, page_v.uri, page_v.rid")->find_all() as $page) {
			if (!in_array($page->rid, $page_rids)) {
				$page_rids[] = $page->rid;
				$pages[$page->title] = $page->uri;
			}
		}

		foreach (O::fa('chunk_asset')->join('page_v','page_v.id','chunk_asset_v.page_vid')->join('page','page.active_vid','page_v.id')->where("asset_rid = {$this->rid}")->select("page_v.title, page_v.uri, page_v.rid")->find_all() as $page) {
			if (!in_array($page->rid, $page_rids)) {
				$page_rids[] = $page->rid;
				$pages[$page->title] = $page->uri;
			}
		}

		foreach (Relationship::find_partners('page',$this)->where("rel1.description = 'featureimage' and rel2.description = 'featureimage'")->find_all() as $page) {
			if (!in_array($page->rid, $page_rids)) {
				$page_rids[] = $page->rid;
				$pages[$page->title] = $page->uri;
			}
		}

		ksort($pages);

		return $pages;
	}

	public function get_metadata_linked_page($key) {
		$ki = Kohana::Instance();
		$page_model = (isset($ki->page_model) ? $ki->page_model : 'site_page');

		$metadata = Tag::find_or_create_tag(1, 'Metadata');
		$asset_metadata = Tag::find_or_create_tag($metadata->rid, 'Assets');

		$metadata = Relationship::find_partner('metadata_v',$asset_metadata)->where("metadata_v.item_tablename='asset' and metadata_v.item_rid=$this->rid and metadata_v.key='$key'")->find();

		if ($metadata->id) {
			if (ctype_digit($metadata->value)) {
				$slideshow_page = O::f($page_model,$metadata->value);
				if ($slideshow_page->rid) {
					return $slideshow_page;
				}
			} else {
				return $metadata->value;
			}
		}

		return false;
	}

	public function get_size() {
		if ($this->filesize >= 1048576) {
			return number_format($this->filesize/1024/1024,1).'MB';
		}
		if ($this->filesize >= 1024) {
			return ceil($this->filesize/1024).'KB';
		}
		return $this->filesize.' bytes';
	}
}
?>
