<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates		www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?php
include_once('hoopbasemodel.php');

class chunk_linkset_v_Model extends Hoopbasemodel {
	public function get_chunk($pagerid, $slotname, $template, $editable=true, $cms=false) {
		$ki = Kohana::instance(); $data = Array();
		$data['template'] = $template; $data['slotname'] = $slotname; $data['slottype'] = 'linkset';

		$cache_enabled = Kohana::config('cache.linkset_slots');

		if ($cache_enabled) {
			$cache = Cache::instance();
			$cache_name = 'get_chunk_linkset_'.$pagerid.'_'.$slotname.'_'.$template.'_'.$editable.'_'.$cms;
			foreach ($_GET as $key => $value) {
				if (is_string($value)) {
					$cache_name .= '_'.$value;
				}
			}

			if (strlen($cache_name) >200) {
				$cache_enabled = false;
			}
		}

		# not preview
		if (!isset($ki->page->rid) || !$ki->page->rid) {
			$ki->page = O::f('cms_page',$pagerid);
		}
		$pagevid = ($pagerid != $ki->page->rid) ? O::fa('page', $pagerid)->id : $ki->page->vid;

		$rc = RequestCache::Instance();
		$rc->result = true;

		Event::run('linksetchunk.before_perm_check',$pagerid);

		$ok = $rc->result;

		if (Permissions::may_i('read',$ki->page)) {
			if (($editable)and(Permissions::may_i('write',$ki->page)and($ok))) {
				# handle cms chunk
				$data['chunk'] = O::fa('chunk_linkset')->find_by_page_vid_and_slotname($pagevid, $slotname);
				if ($data['chunk']->rid != '') {
					$target = O::fa('linkset_links')->find_by_chunk_linkset_rid($data['chunk']->rid);
				}
				if ((!isset($data['chunk']) or ($data['chunk']->id == '')  or  ((!isset($target) or (!$target->rid))))and($ki->uri->segment(1) != '_ajax'))  {
					# if haven't been able to find the chunk or the target page, return the defaultslot template
					return Site_Controller::addcmsclasses(new View("site/slots/subtpl_defaultslot_linkset_" . $template,$data),'',$data);
				} else {
					# otherwise return the slottype template
					if ($cms) {
						if ($cache_enabled) {
							$cache_name .= "_cms_cms";
							if (!$chunk_data = $cache->get($cache_name)) {
								$data['chunk'] = O::fa('chunk_linkset')->find_by_page_vid_and_slotname($pagevid, $slotname);
								if ($data['chunk']->rid != '') {
									$target = O::fa('linkset_links')->find_by_chunk_linkset_rid($data['chunk']->rid);
								}
								$view = new View("cms/slots/subtpl_slottype_linkset_" . $template,$data);
								$chunk_data = site_Controller::addcmsclasses($view->render(),'',$data);
								$cache->set($cache_name,$chunk_data,array('linkset_slots'),0);
							}
							return $chunk_data;
						}
						$data['chunk'] = O::fa('chunk_linkset')->find_by_page_vid_and_slotname($pagevid, $slotname);
						if ($data['chunk']->rid != '') {
							$target = O::fa('linkset_links')->find_by_chunk_linkset_rid($data['chunk']->rid);
						}
						return Site_Controller::addcmsclasses(new View("cms/slots/subtpl_slottype_linkset_" . $template,$data),'',$data);
					} else {
						if ($cache_enabled) {
							$cache_name .= "_cms_site";
							if (!$chunk_data = $cache->get($cache_name)) {
								$data['chunk'] = O::fa('chunk_linkset')->find_by_page_vid_and_slotname($pagevid, $slotname);
								if ($data['chunk']->rid != '') {
									$target = O::fa('linkset_links')->find_by_chunk_linkset_rid($data['chunk']->rid);
								}
								$view = new View("site/slots/subtpl_slottype_linkset_" . $template,$data);
								$chunk_data = site_Controller::addcmsclasses($view->render(),'',$data);
								$cache->set($cache_name,$chunk_data,array('linkset_slots'),0);
							}
							return $chunk_data;
						}
						$data['chunk'] = O::fa('chunk_linkset')->find_by_page_vid_and_slotname($pagevid, $slotname);
						if ($data['chunk']->rid != '') {
							$target = O::fa('linkset_links')->find_by_chunk_linkset_rid($data['chunk']->rid);
						}
						return Site_Controller::addcmsclasses(new View("site/slots/subtpl_slottype_linkset_" . $template,$data),'',$data);
					}
				}
			} else {
				# handle site chunk
				$data['chunk'] = O::fa('chunk_linkset')->find_by_page_vid_and_slotname($pagevid, $slotname);
				if ($data['chunk']->rid != '') {
					$target = O::fa('linkset_links')->find_by_chunk_linkset_rid($data['chunk']->rid);
				}
				if (!isset($data['chunk']) or ($data['chunk']->id == '')	or	((!isset($target) or ($target->id == ''))) ) {
					return '';
				} else {
					if ($cache_enabled) {
						$cache_name .= "_site";
						if (!$chunk_data = $cache->get($cache_name)) {
							$view = new View("site/slots/subtpl_slottype_linkset_" . $template,$data);
							$chunk_data = $view->render();
							$cache->set($cache_name,$chunk_data,array('linkset_slots'),0);
						}
						return $chunk_data;
					}
					return new View("site/slots/subtpl_slottype_linkset_" . $template, $data);
				}
			}
		} 
		return '';
	}
}
?>
