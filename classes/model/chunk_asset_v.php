<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates		www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?php
include_once('hoopbasemodel.php');

class chunk_asset_v_Model extends Hoopbasemodel {
	public function get_chunk($pagerid, $slotname, $template,$preview_target_rid=false,$editable=true,$remove=false) {
		
		$ki = Kohana::instance(); $data = Array();
		$data['template'] = $template; $data['slotname'] = $slotname; $data['slottype'] = 'asset';

		$cache_enabled = Kohana::config('cache.asset_slots');

		if ($cache_enabled) {
			$cache = Cache::instance();
			if (isset($_GET) && count($_GET)>0) {
				$get = array();
				foreach ($_GET as $key => $value) {
					if (!is_array($value)) $get[$key] = $value;
				}
				$get = sha1(implode('-',$get));
			} else {
				$get = null;
			}
			$cache_name = 'get_chunk_asset_'.$pagerid.'_'.$slotname.'_'.$template.'_'.$preview_target_rid.'_'.$editable.'_'.$remove.$get;

			if (strlen($cache_name) >200) {
				$cache_enabled = false;
			}
		}
		
		if ($preview_target_rid) {
			# preview (via webgetchunk)
			if ($cache_enabled) {
				$cache_name .= '_preview';
				if (!$chunk_data = $cache->get($cache_name)) {
					$data['target'] = O::fa('asset')->find_by_rid($preview_target_rid);
					$data['chunk'] = O::fa('chunk_asset');
					$data['chunk']->slotname = $slotname;
					$view = new View("site/slots/subtpl_slottype_asset_" . $template,$data);
					$chunk_data = site_Controller::addcmsclasses($view->render(),'',$data);
					$cache->set($cache_name,$chunk_data,array('asset_slots'),0);
				}
				return $chunk_data;
			}
			$data['target'] = O::fa('asset')->find_by_rid($preview_target_rid);
			$data['chunk'] = O::fa('chunk_asset');
			$data['chunk']->slotname = $slotname;
			
			return Site_Controller::addcmsclasses(new View("site/slots/subtpl_slottype_asset_" . $template,$data),'',$data);
			
		} else {
			# not preview
			
			if (!isset($ki->page->rid) || !$ki->page->rid) {
				$ki->page = O::f('cms_page',$pagerid);
			}
			
			$pagevid = ($pagerid != $ki->page->rid) ? O::fa('page', $pagerid)->id : (isset($ki->page->vid) ? $ki->page->vid : $ki->page->id);

			$rc = RequestCache::Instance();
			$rc->result = true;

			Event::run('assetchunk.before_perm_check',$pagerid);

			$ok = $rc->result;

			if (Permissions::may_i('read',$ki->page)) {
				if (($editable)and(Permissions::may_i('write',$ki->page)and($ok))) {
					# handle cms chunk
					$data['chunk'] = O::fa('chunk_asset')->find_by_page_vid_and_slotname($pagevid, $slotname);
					if (!$remove and $data['chunk']->rid) {
						$data['target'] = O::fa('asset')->find_by_rid($data['chunk']->asset_rid);
					}
					if ($remove or (!isset($data['chunk']) or ($data['chunk']->id == '')	or	((!isset($data['target']) or ($data['target']->id == '')))) ) {
						# set slotname on empty chunk
						if (!$data['chunk']->slotname) {
							$data['chunk']->slotname = $slotname;
						}
						# if remove is set or we haven't been able to find the chunk or the target page, return the defaultslot template
						
						return Site_Controller::addcmsclasses(new View("site/slots/subtpl_defaultslot_asset_" . $template,$data),'',$data);
					} else {
						# otherwise return the slottype template
						if ($cache_enabled) {
							$cache_name .= "_cms";
							if (!$chunk_data = $cache->get($cache_name)) {
								$data['chunk'] = O::fa('chunk_asset')->find_by_page_vid_and_slotname($pagevid, $slotname);
								if (!$remove and $data['chunk']->rid) {
									$data['target'] = O::fa('asset')->find_by_rid($data['chunk']->asset_rid);
								}
								$view = new View("site/slots/subtpl_slottype_asset_" . $template,$data);
								$chunk_data = site_Controller::addcmsclasses($view->render(),'',$data);
								$cache->set($cache_name,$chunk_data,array('asset_slots'),0);
							}
							return $chunk_data;
						}
						$data['chunk'] = O::fa('chunk_asset')->find_by_page_vid_and_slotname($pagevid, $slotname);
						if (!$remove and $data['chunk']->rid) {
							$data['target'] = O::fa('asset')->find_by_rid($data['chunk']->asset_rid);
						}
						return Site_Controller::addcmsclasses(new View("site/slots/subtpl_slottype_asset_" . $template,$data),'',$data);
					}
				} else {
					# handle site chunk
					$data['chunk'] = O::fa('chunk_asset')->find_by_page_vid_and_slotname($pagevid, $slotname);
					if (!$remove and $data['chunk']->rid) {
						$data['target'] = O::fa('asset')->find_by_rid($data['chunk']->asset_rid);
					}
					if (!isset($data['chunk']) or ($data['chunk']->id == '')	or	((!isset($data['target']) or ($data['target']->id == ''))) ) {
						return '';
					} else {
						if ($cache_enabled) {
							$cache_name .= "_site";
							if (!$chunk_data = $cache->get($cache_name)) {
								$chunk_data = new View("site/slots/subtpl_slottype_asset_" . $template, $data);
								$chunk_data = $chunk_data->render();
								$cache->set($cache_name,$chunk_data,array('asset_slots'),0);
							}
							return $chunk_data;
						}
						return new View("site/slots/subtpl_slottype_asset_" . $template, $data);
					}
				}
			}
			return '';
		}
	}

	public function get_caption($pagerid, $asset_rid, $slotname, $htmlheader='', $htmlfooter='', $disablededitoroptions='', $editable=true,$editor='jwysiwyg') {
		$rc = RequestCache::instance();
		if (!isset($rc->chunk_text_pages)) $rc->chunk_text_pages = array();

		if (isset($rc->chunk_text_pages[$pagerid])) {
			$page = $rc->chunk_text_pages[$pagerid];
		} else {
			$rc->chunk_text_pages[$pagerid] = $page = O::f('cms_page',$pagerid);
		}
		$can_read = Permissions::may_i('read',$page);
		$can_write = Permissions::may_i('write',$page);

		if (!$can_read) return;

		$asset = O::fa('asset',$asset_rid);
		if (!$asset->rid) {
			throw new Kohana_Exception('sledge.asset_not_found', $asset_rid);
		}

		if ($can_write) {
			Event::$data = true;

			$rc = RequestCache::Instance();
			$rc->slotname = $slotname;
			$rc->result = true;

			Event::run('assetchunk.before_perm_check',$pagerid);

			$can_write = $rc->result;
		}

		$cache_enabled = Kohana::config('cache.asset_slots');

		if ($cache_enabled) {
			$cache = Cache::instance();
			$cache_name = 'get_asset_caption_text_'.$pagerid.'_'.$asset->rid.'_'.sha1($htmlheader).'_'.sha1($htmlfooter).'_'.sha1($disablededitoroptions).'_'.$editable.'_'.$editor.'_'.(($can_write) ? 'cms' : 'site');
			if ($text = $cache->get($cache_name)) {
				return $text;
			}

			if (strlen($cache_name) >200) {
				$cache_enabled = false;
			}
		}

		$ki = Kohana::instance(); $data = Array();
		$data['disablededitoroptions'] = $disablededitoroptions;
		if (!isset($ki->page->rid) || !$ki->page->rid) {
			$ki->page = $page;
		}
		$pagevid = ($pagerid != $ki->page->rid) ? O::fa('page', $pagerid)->id : (isset($ki->page->vid) ? $ki->page->vid : $ki->page->id);

		$data = array();
		$data['slotname'] = $slotname;
		$data['slottype'] = 'asset-caption';
		$data['disablededitoroptions'] = $disablededitoroptions;
		$data['chunk'] = O::fa('chunk_asset')->find_by_page_vid_and_slotname_and_asset_rid($pagevid, $slotname, $asset_rid);

		if (!$data['chunk']->rid || $data['chunk']->text == "") {
			$metadata = Tag::find_or_create_tag(1,'Metadata');
			$assets = Tag::find_or_create_tag($metadata->rid,'Assets');

			# Use default caption text if set
			$m = Relationship::find_partner('metadata_v',$assets)->where("metadata_v.item_tablename = 'asset' and metadata_v.item_rid = $asset_rid and key='default-caption'")->find();
			if ($m->id) {
				$data['chunk']->text = $m->value;
			}
		}

		if ( ($editable) and ($can_write)) {
			# handle cms chunk

			$htmlheader = Site_Controller::addcmsclasses($htmlheader, $editor,$data);
			$text = $htmlheader.$data['chunk']->text.$htmlfooter;

			if ($cache_enabled && !@$key_restricted) {
				$cache->set($cache_name,$text,array('asset_slots'),0);
			}
			return $text;
		} else {
			# handle site chunk
			$text = ($data['chunk']->text != '') ? $htmlheader.$data['chunk']->text.$htmlfooter : '';

			if ($cache_enabled && !@$key_restricted) {
				$cache->set($cache_name,$text,array('asset_slots'),0);
			}

			return $text;
		}
	}
}
?>
