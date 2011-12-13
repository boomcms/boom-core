<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates		www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?php
include_once('hoopbasemodel.php');

class chunk_text_v_Model extends Hoopbasemodel {

	public $image_height = false;
	public $image_width = false;

	public function __get($key) {
		if($key=='text') {
			if (isset($this->object[$key])) {
				$value = $this->object[$key];
				$value = preg_replace_callback('{<a.*?href=[\'|"](.*?)[\'|"].*?>(.*?)<\/a>}s', array($this, 'unmungelink'), $value);
				$value = preg_replace_callback('{<img([^>]*)>}', array($this, 'unmungeimage'), $value);
				return $value;
			}
		}
		return parent::__get($key);
	}

	public function unmunge($text) {
		$text = preg_replace_callback('{<a.*?href=[\'|"](.*?)[\'|"].*?>(.*?)<\/a>}s', array($this, 'unmungelink'), $text);
		$text = preg_replace_callback('{<img([^>]*)>}', array($this, 'unmungeimage'), $text);
		return $text;
	}

	public function replace_unicode_chars($subject, $chars, $replacement) {
		foreach ($chars as $char) {
			$subject = str_replace(Misc::unichr($char), $replacement, $subject);
		}
		return $subject;
	}

	public function __set($key, $value) {
		if($key=='text') {
				//Quotes: Replace smart double quotes with straight double quotes.
				$value = $this->replace_unicode_chars($value, array('201C','201D','201E','201F','2033','2036'), '"');

				//Quotes: Replace smart single quotes and apostrophes with straight single quotes.		
				$value = $this->replace_unicode_chars($value, array('2018','2019','201A','201B','2032','2035'), "'");

				// remove template from templated asset stuff
				// important note: we are only munging image assets that have been inserted with a wrapper template (this is to accomodate tinymce)
				$value = preg_replace_callback('{<div>\s*<\!--assetwrapperstart-->(.*)<\!--assetwrapperend-->\s*<\/div>}sUi', array($this, 'unwrap'), $value);

				// munge internal links and assets
				$value = preg_replace_callback('{<a.*?href=[\'|"](.*?)[\'|"].*?>(.*?)<\/a>}s', array($this, 'mungelink'), $value);

				// HTML Tidy cleanup
				if ($this->slotname == 'bodycopy') {
					$tidy = new Tidy();
					$tidy->parseString($value, Kohana::config('htmltidy.config'), Kohana::config('htmltidy.charset'));
					//$tidy->cleanRepair();
					$tidy = preg_replace("/<div>/", "<p>", $tidy);
					$tidy = preg_replace("/<div[^>]+>/", "<p>", $tidy); // replace opening div with p
					$tidy = preg_replace("/<\/div>/", "</p>", $tidy); // replace closing div with p
					$tidy = preg_replace("/\sclass=\"\"/", "", $tidy); // remove empty classes
					$tidy = preg_replace("/\sstyle=\"\"/", "", $tidy); // remove empty style declarations
					$value = $tidy;
				}

				// No text elements should ever contain script or inline styling
				$value = preg_replace('/<style.*?>.*?<\/style>/si','',$value);
				$value = preg_replace('/<script.*?>.*?<\/script>/si','',$value);
		}

		return parent::__set($key,$value);
	}

	public function unwrap($matches) {
		$array = array();
		preg_match("{<\!--assetstart-->(.*)<\!--assetend-->}sUi", $matches[1], $array);
		$image = isset($array[1]) ? $array[1] : '';
		// only munge images that were inserted as a template
		return preg_replace_callback('{<img([^>]*)>}', array($this, 'mungeimage'), $image);
	}
	
	public function unmungeimage($matches) {
		$image = $matches[0];
		$image_src = $this->get_tag_attribute($image, 'src');
		$image_alt = $this->get_tag_attribute($image, 'alt');

		if (preg_match("/^hoopdb\:\/\/image\//", $image_src)) {
			$image_rid = preg_replace("/^hoopdb\:\/\/image\//", "", $image_src);
			if ($this->image_width && $this->image_height) {
				return Asset::web_get_asset($image_rid, $this->image_width, $this->image_height, Kohana::config('core.asset_quality'), 0, true, false, 'default');
			} else if ($this->image_width) {
				$height = Asset::scale_image(Kohana::config('core.asset_width'),Kohana::config('core.asset_height'),$this->image_width);
				$height = $height[1];
				return Asset::web_get_asset($image_rid, $this->image_width, $height, Kohana::config('core.asset_quality'), 0, true, false, 'default');
			} else if ($this->image_height) {
				$width =	Asset::scale_image(Kohana::config('core.asset_width'),Kohana::config('core.asset_height'),'',$this->image_height);
				$width = $width[0];
				return Asset::web_get_asset($image_rid, $width, $this->image_height, Kohana::config('core.asset_quality'), 0, true, false, 'default');
			} else {
				return Asset::web_get_asset($image_rid, Kohana::config('core.asset_width'), Kohana::config('core.asset_height'), Kohana::config('core.asset_quality'), 0, true, false, 'default');
			}
		} else {
			return $image;
		}
	}

	public function mungeimage($matches) {
		$image = $matches[0];
		$image_src = $this->get_tag_attribute($image, 'src');
		$image_alt =	$this->get_tag_attribute($image, 'alt');	
		$image_rid = preg_replace("/^.*get_asset\/([0-9]+).*$/", "$1", $image_src);
		return "<img src=\"hoopdb://image/$image_rid\" alt=\"$image_alt\" />";
	}

	public function mungelink($matches) {
		$link = $matches[0];
		$href = $matches[1];
		$rel = $this->get_tag_attribute($link,'rel');
		$style = $this->get_tag_attribute($link,'style');

		// some anchors may contain the style attribute, and we cannot store inline style declarations in the db
		// so this span takes care of that
		$span_open = $style != '' ? '<span style="'.$style.'">' : '';
		$span_close = $style != '' ? '</span>' : '';

		if (preg_match("/get_asset/", $href)) {
			$class = str_replace(' ', '_', $this->get_tag_attribute($link, 'class'));
			return "<a href=\"hoopdb://asset/$rel/class/$class\">".$span_open.$this->get_tag_data($link).$span_close."</a>";
		} else {
			if (ctype_digit($rel)) {
				return str_replace($href,'hoopdb://page/'.$rel,$link);
			} else {
				return $link;
			}
		}
	}
	
	public function unmungelink($matches) {
		$link = $matches[0];
		$href = $matches[1];

		if (preg_match("/^hoopdb\:\/\/page\//", $href)) {
			$page_rid = preg_replace("/^hoopdb\:\/\/page\//", "", $href);
			$p = O::fa('page', $page_rid); 
			if (!class_exists('Cms_page_manager_Controller', false) and $p->ref_page_v_status_rid != 4) {
				return $this->get_tag_data($link);
			} else {
				return str_replace($href,$p->absolute_uri(),$link);
			}
		} elseif (preg_match("/^hoopdb\:\/\/asset\//", $href)) {
			$db_str = preg_replace("/^hoopdb\:\/\//", "", $href);
			$db_data = explode('/', $db_str);
			$asset_rid = $db_data[1];
			$asset_class = str_replace('_', ' ', $db_data[3]);
			$asset = O::fa('asset', $asset_rid);
			$asset_title = $asset->description == '' ? $asset->title : $asset->description;
			$new_href = class_exists('Cms_page_manager_Controller', false) ? '/cms_page_manager/get_asset/'.$asset_rid : '/get_asset/'.$asset_rid;
			$new_rel = class_exists('Cms_page_manager_Controller', false) ? ' rel="'.$asset->rid.'"' : '';
			$ssl = ($this->page_is_ssl()) ? 's' : null;

			if (isset($db_data[4]) && $db_data[4]==0) {
				return Asset::web_get_asset($asset_rid,false,false,false,0,true,false);
			} else {
				return Asset::web_get_asset($asset_rid);
			}

			# return Asset::web_get_asset($image_rid, 300, 300, 85, 0, true, false, 'default');
			# return "<a title=\"$asset_title\" href=\"http$ssl://".$_SERVER['HTTP_HOST']."$new_href\" $new_rel class=\"$asset_class\">".$this->get_tag_data($link)."</a>";
		} else {
			return $link;
		}
	}

	public function page_is_ssl() {
		ORM::reset();
		$page_v = O::f('page_v',$this->object['page_vid']);
		if ($page_v->ssl_only == 't' || Kohana::config('core.force_ssl')) {
			return true;
		}
		return false;
	}
	
	public function get_tag( $tag, $xml ) {
		$tag = preg_quote($tag); preg_match_all('{<'.$tag.'[^>]*>(.*?)</'.$tag.'>}', $xml, $matches, PREG_PATTERN_ORDER); 
		if (isset($matches[0])) {
			return $matches[0];
		} else {
			return ".";
		}
	}
	
	public function get_tag_data( $tagdata ) {
		preg_match_all('{<a[^>]*>(.*?)</a>}', $tagdata, $tagmatches); 
		if (isset($tagmatches[1][0])) {
			return $tagmatches[1][0];
		} else {
			return ".";
		}
	}
	
	public function get_tag_attribute($tag, $attribute) {
		if (preg_match('/'.$attribute.'\s*=\s*\'(.*?)\'/',$tag,$m) || preg_match('/'.$attribute.'\s*=\s*"(.*?)"/',$tag,$m)) {
		return $m[1];
		}
	}

	// This grabs one paragraph of the bodycopy chunk to use for the search results page
	public function get_summary() {
		$x = explode("</p>", $this->text); return strip_tags($x[0]);
	}
	public function get_chunk($pagerid, $slotname, $htmlheader='',$htmlfooter='',$disablededitoroptions='', $editable=true,$editor='jwysiwyg', $image_width=false, $image_height=false, $key_restricted=false) {
	
		if ($image_width) $this->image_width = $image_width;
		if ($image_height) $this->image_height = $image_height;

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

		if ($can_write) {
			Event::$data = true;

			$rc = RequestCache::Instance();
			$rc->slotname = $slotname;
			$rc->result = true;

			Event::run('textchunk.before_perm_check',$pagerid);

			$can_write = $rc->result;
		}

		$cache_enabled = Kohana::config('cache.text_slots');

		$cache_enabled = false;

		if ($cache_enabled) {
			$cache = Cache::instance();
			$cache_name = 'get_chunk_text_'.$pagerid.'_'.$slotname.'_'.sha1($htmlheader).'_'.sha1($htmlfooter).'_'.sha1($disablededitoroptions).'_'.$editable.'_'.$editor.'_'.(($can_write) ? 'cms' : 'site');
			if ($text = $cache->get($cache_name)) {
				return $text;
			}

			if (strlen($cache_name) >200) {
				$cache_enabled = false;
			}
		}

		$ki = Kohana::instance(); $data = Array();
		$data['slotname'] = $slotname; $data['slottype'] = 'text';
		$data['disablededitoroptions'] = $disablededitoroptions;
		if (!isset($ki->page->rid) || !$ki->page->rid) {
			$ki->page = O::f('cms_page',$pagerid);
		}
		
		if ($pagerid != $ki->page->rid) {
			$pagevid = O::fa('page', $pagerid)->id;
		} else { 
			if (isset($ki->page->vid)) {
				$pagevid = $ki->page->vid;
			} else {
				$pagevid = $ki->page->id;
			}
		}
		
		$data['chunk'] = $this->find_by_page_vid_and_slotname($pagevid, $slotname);
		
		$rc = RequestCache::Instance();
		$rc->chunk_text_data = $data;
		$rc->chunk_text_data['chunk']->slotname = $slotname;

		Event::run('chunktext.gottext');

		$data = $rc->chunk_text_data;

		if ( ($editable) and ($can_write)) {
			# handle cms chunk
				
			$htmlheader = Site_Controller::addcmsclasses($htmlheader, $editor,$data);
			$text = $htmlheader.$data['chunk']->text.$htmlfooter;

			if ($cache_enabled && !$key_restricted) {
				$cache->set($cache_name,$text,array('text_slots'),0);
			}

			if ($key_restricted) {

				$slots = Tag::find_or_create_tag(1,'Slots');
				$text_chunks = Tag::find_or_create_tag($slots->rid,'Text chunks');
				if (substr($slotname,0,8) == "boolean-") {
					
					return new View('cms/slots/subtpl_chunk_text_restricted_boolean',array('slotname'=>$slotname,'text'=>$text));
				
				} else {
				
				$kr = Relationship::find_partner('key_restrictions_v',$text_chunks)->where("key_name = '$slotname'")->find();
				if ($kr->id) {
					$type = O::fa('ref_value_type',$kr->ref_value_type_rid);
					return new View('cms/slots/subtpl_chunk_text_restricted_'.$type->name,array('slotname'=>$slotname,'text'=>$text));
				}
				
				}
			}
			return $text;
		} else {
			# handle site chunk
			$text = ($data['chunk']->text != '') ? $htmlheader.$data['chunk']->text.$htmlfooter : '';

			if ($cache_enabled && !$key_restricted) {
				$cache->set($cache_name,$text,array('text_slots'),0);
			}

			if ($key_restricted) { 
				$slots = Tag::find_or_create_tag(1,'Slots');
				$text_chunks = Tag::find_or_create_tag($slots->rid,'Text chunks');

				$kr = Relationship::find_partner('key_restrictions_v',$text_chunks)->where("key_name = '$slotname'")->find();
				if ($kr->id) {
					$type = O::fa('ref_value_type',$kr->ref_value_type_rid);
					$view = new View('site/slots/subtpl_chunk_text_restricted_'.$type->name,array('slotname'=>$slotname,'text'=>$text),null,false);
					if ($view->kohana_filename) {
						return $view;
					}
				}
			}
			return $text;
		}
	}
}
?>
