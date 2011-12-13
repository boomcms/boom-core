<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates		www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?php
include_once('hoopbasemodel.php');

class chunk_tag_v_Model extends Hoopbasemodel {
	public function get_chunk($pagerid, $slotname, $template, $preview_target_rid=false, $editable=true, $remove=false, $toplevel_rid=0) {
		$ki = Kohana::instance(); $data = Array();
		$data['template'] = $template; 
		$data['slotname'] = $slotname; 
		$data['slottype'] = 'tag';

		if ($preview_target_rid) {
			# preview (via webgetchunk)
			$data['target'] = O::fa('tag')->find_by_rid($preview_target_rid);
			$data['chunk'] = O::fa('chunk_tag');
			$data['chunk']->slotname = $slotname;
			$data['toplevel_rid'] = $toplevel_rid;
			return site_Controller::addcmsclasses(new View("site/slots/subtpl_slottype_tag_" . $template,$data),'',$data);
		} else {
			# not preview
			if (!isset($ki->page->rid) || !$ki->page->rid) {
				$ki->page = O::f('cms_page',$pagerid);
			}

			$pagevid = ($pagerid != $ki->page->rid) ? O::fa('page', $pagerid)->id : (isset($ki->page->vid) ? $ki->page->vid : $ki->page->id);
			$data['chunk'] = O::fa('chunk_tag')->find_by_page_vid_and_slotname($pagevid, $slotname);
			$data['toplevel_rid'] = $toplevel_rid;

			if (!$remove and $data['chunk']->rid != '') {
				$data['target'] = O::fa('tag')->find_by_rid($data['chunk']->target_tag_rid);
			}

			$rc = RequestCache::Instance();
			$rc->result = true;

			Event::run('tagchunk.before_perm_check',$pagerid);

			$ok = $rc->result;

			if (Permissions::may_i('read',$ki->page)) {
				if (($editable)and(Permissions::may_i('write',$ki->page)and($ok))) {
					# handle cms chunk
					if ($remove or (!isset($data['chunk']) or ($data['chunk']->id == '') or ((!isset($data['target']) or ($data['target']->id == '')))) ) {
						# set slotname on empty chunk
						if (!$data['chunk']->slotname) {
							$data['chunk']->slotname = $slotname;
						}
						# if remove is set or we haven't been able to find the chunk or the target page, return the defaultslot template
						return site_Controller::addcmsclasses(new View("site/slots/subtpl_defaultslot_tag_" . $template,$data),'',$data);
					} else {
						# otherwise return the slottype template
						return site_Controller::addcmsclasses(new View("site/slots/subtpl_slottype_tag_" . $template,$data),'',$data);
					}
				} else {
					# handle site chunk
					if (!isset($data['chunk']) or (!$data['chunk']->id)  or  ((!isset($data['target']) or (!$data['target']->id))) ) {
						return '';
					} else {
						return new View("site/slots/subtpl_slottype_tag_" . $template, $data);
					}
				}
			} 
			return '';
		}
	}
}
?>
