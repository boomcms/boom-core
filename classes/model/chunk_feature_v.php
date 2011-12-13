<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates		www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?php
include_once('hoopbasemodel.php');

class chunk_feature_v_Model extends Hoopbasemodel {
	public function get_chunk($pagerid, $slotname, $template,$preview_target_rid=false,$editable=true,$remove=false) {
		$ki = Kohana::instance(); $data = Array();
		$data['template'] = $template; $data['slotname'] = $slotname; $data['slottype'] = 'feature';

		$cache_enabled = Kohana::config('cache.feature_slots');

		if ($cache_enabled) {
			$cache = Cache::instance();
			$cache_name = 'get_chunk_feature_'.$pagerid.'_'.$slotname.'_'.$template.'_'.$preview_target_rid.'_'.$editable.'_'.$remove;
			foreach ($_GET as $key => $value) {
				if (is_string($value)) {
					$cache_name .= '_'.$value;
				}
			}
			if (strlen($cache_name) >200) {
				$cache_enabled = false;
			}
		}

		if ($preview_target_rid) {
			# preview (via webgetchunk)
			if ($cache_enabled) {
				$cache_name .= '_preview';
				if (!$chunk_data = $cache->get($cache_name)) {
					$data['target'] = O::f('cms_page')->find_by_rid($preview_target_rid);
					$data['chunk'] = O::fa('chunk_feature');
					$data['chunk']->slotname = $slotname;
					$view = new View("site/slots/subtpl_slottype_feature_" . $template,$data);
					$chunk_data = site_Controller::addcmsclasses($view->render(),'',$data);
					$cache->set($cache_name,$chunk_data,array('feature_slots'),0);
				}
				return $chunk_data;
			}

			$data['target'] = O::f('cms_page')->find_by_rid($preview_target_rid);
			$data['chunk'] = O::fa('chunk_feature');
			$data['chunk']->slotname = $slotname;

			return site_Controller::addcmsclasses(new View("site/slots/subtpl_slottype_feature_" . $template,$data),'',$data);
		} else {
			# not preview
			if (!isset($ki->page->rid) || !$ki->page->rid) {
				$ki->page = O::f('cms_page',$pagerid);
			}

			$pagevid = ($pagerid != $ki->page->rid) ? O::fa('page', $pagerid)->id : (isset($ki->page->vid) ? $ki->page->vid : $ki->page->id);

			$rc = RequestCache::Instance();
			$rc->result = true;

			Event::run('featurechunk.before_perm_check',$pagerid);

			$ok = $rc->result;

			if (Permissions::may_i('read',$ki->page)) {
				if (($editable)and(Permissions::may_i('write',$ki->page)and($ok))) {
					# handle cms chunk
					$data['chunk'] = O::fa('chunk_feature')->find_by_page_vid_and_slotname($pagevid, $slotname);
					if (!$remove and $data['chunk']->rid != '') {
						$data['target'] = O::f($ki->page_model)->find_by_rid($data['chunk']->target_page_rid);
					}

					if ($remove or (!isset($data['chunk']) or ($data['chunk']->id == '')	or	((!isset($data['target']) or ($data['target']->id == '')))) ) {
						# set slotname on empty chunk
						if (!$data['chunk']->slotname) {
							$data['chunk']->slotname = $slotname;
						}
						# if remove is set or we haven't been able to find the chunk or the target page, return the defaultslot template
						return site_Controller::addcmsclasses(new View("site/slots/subtpl_defaultslot_feature_" . $template,$data),'',$data);
					} else {
						# otherwise return the slottype template
						if ($cache_enabled) {
							$cache_name .= "_cms";
							if (!$chunk_data = $cache->get($cache_name)) {
								$data['chunk'] = O::fa('chunk_feature')->find_by_page_vid_and_slotname($pagevid, $slotname);
								if (!$remove and $data['chunk']->rid != '') {
									$data['target'] = O::f($ki->page_model)->find_by_rid($data['chunk']->target_page_rid);
								}

								$view = new View("site/slots/subtpl_slottype_feature_" . $template,$data);
								$chunk_data = site_Controller::addcmsclasses($view->render(),'',$data);
								$cache->set($cache_name,$chunk_data,array('feature_slots'),0);
							}
							return $chunk_data;
						}
						$data['chunk'] = O::fa('chunk_feature')->find_by_page_vid_and_slotname($pagevid, $slotname);
						if (!$remove and $data['chunk']->rid != '') {
							$data['target'] = O::f($ki->page_model)->find_by_rid($data['chunk']->target_page_rid);
						}
						return site_Controller::addcmsclasses(new View("site/slots/subtpl_slottype_feature_" . $template,$data),'',$data);
					}
				} else {
					# handle site chunk
					$data['chunk'] = O::fa('chunk_feature')->find_by_page_vid_and_slotname($pagevid, $slotname);
					if (!$remove and $data['chunk']->rid != '') {
						$data['target'] = O::f($ki->page_model)->find_by_rid($data['chunk']->target_page_rid);
					}
					if (!isset($data['chunk']) or (!$data['chunk']->id)  or  ((!isset($data['target']) or (!$data['target']->id))) ) {
						return '';
					} else {
						if ($cache_enabled) {
							$cache_name .= "_site";
							if (!$chunk_data = $cache->get($cache_name)) {
								$chunk_data = new View("site/slots/subtpl_slottype_feature_" . $template, $data);
								$chunk_data = $chunk_data->render();
								$cache->set($cache_name,$chunk_data,array('feature_slots'),0);
							}
							return $chunk_data;
						}
						return new View("site/slots/subtpl_slottype_feature_" . $template, $data);
					}
				}
			} 
			return '';
		}
	}
}
?>
