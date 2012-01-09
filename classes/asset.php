<?php

/**
* Defines a decorator class for use with assets. 
* Assets can be one of many different types - video, image, application etc.
* Each type of asset will need different methods for things like retrieval.
* This class therefore creates an 'interface' of which there are different sub-types which we can use for performing actions on the asset.
* This is an example of the decorator pattern.
* @see http://en.wikipedia.org/wiki/Decorator_pattern
*
* @package Asset
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* @todo Most of the methods in here are direct copy and paste from the previous Asset library. They need to be refactored and documented.
*
*/

abstract class Asset {
	/**
	* @access protected
	* @var object
	* Stores an instance of the asset_Model class which we are providing an interface to.
	*/
	protected $_asset;
	
	public function __construct( Model_Asset $asset )
	{
		$this->_asset = $asset;
	}
	
	/**
	* Used to initialise the decorator.
	* Determines which decoration we need from the asset_type name
	* @param string $type The type of asset
	* @param object $asset The asset we're decorating
	* @return Asset
	*/
	public static function factory( $type, Model_Asset $asset ) {
		switch( $type ) {
			case 'image':
				return new Asset_Image( $asset );
				break;
			case 'video':
				return new Asset_Video( $asset );
				break;
			case 'mp3':
				return new Asset_MP3( $asset );
				break;
			default:
				return new Asset_Default( $asset );*/
		}
	}
	
	public function instance()
	{
		return $this->_asset;
	}
	
	/**
	* Method to show the asset in a page
	*/
	abstract function show();
	
	/**
	* Send headers which tell the browser whether or not it should cache the asset.
	* @return void
	*/ 
	public function sendCacheHeaders() {
		$cache_duration = Kohana::config('core.cache_default_assets');
		$cache_duration = ($cache_duration * 60);  // duration is stored in the database in minutes, but is required here in seconds
		
		if (!($cache_duration === null || $cache_duration == 0) || isset($_SERVER['HTTPS'])) {
			// Don't cache the asset.
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
			header("Last-modified: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		} else {
			header("Cache-Control: must-revalidate, max-age=".$cache_duration);
			header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$cache_duration) . ' GMT');
			header("Last-modified: " . gmdate('D, d M Y H:i:s', time()));
		}
	}
	
	public function readfile () {
		ob_clean();
		flush(); 
		$fp = fopen(Kohana::config('core.assetpath').'/'.$this->_asset->current_version->filename,'r');
		while ($data = fread($fp, 10240)) {
			echo $data;
			ob_flush();
		}
		
		fclose($fp);
	}
	
	public function import($path, $title = false, $tag = false) {
		if (preg_match('/\//',$path)) {
			$ex = explode('/',$path);
			$file = $ex[count($ex)-1];
		} else {
			$file = $path;
		}

		// Get the file's mimetype.
		$finfo = new finfo( FILEINFO_MIME );
		$mimetype = $finfo->file( $filename );
		$finfo->close();

		list($main_mimetype, $sub_mimetype) = explode('/', preg_replace("/;.*/", '', $mimetype));

		$post_before = $_POST;
		$files_before = $_FILES;

		$_POST = array(
			'replace_asset_rid' => '0',
			'ref_status_rid' => '2'
		);

		if (is_object($tag) && $tag->rid) {
			$_POST['tag'] = $tag->rid;
		} else if ($tag) {
			$_POST['tag'] = $tag;
		}

		@copy($path, $path.'.tmp');

		$_FILES = array(
			'uploadfile' => array(
				'name' => $file,
				'type' => "$main_mimetype/$sub_mimetype",
				'tmp_name' => $path.'.tmp',
				'error' => UPLOAD_ERR_OK,
				'size' => filesize($path)
			)
		);

		if (Asset::allowed_to_upload($_FILES['uploadfile'])) {
			if (!$title) {
				$_POST['title'] = preg_replace('/\.[a-zA-Z0-9]+$/','',$file);
			} else {
				$_POST['title'] = $title;
			}
			
			$asset_rid = Asset::put_asset_data($_FILES['uploadfile'], 0, false, true);
						
			Asset::move_file($asset_rid, $path.'.tmp');		
			Asset::save_metadata('asset', $asset_rid, '0', false);
			if ($asset_rid && @$_POST['tag']) {
				Relationship::create_relationship(array('tag','asset'),array($_POST['tag'],$asset_rid));
			}
			
			$v = O::f('activitylog_v');
			$v->remotehost = @$_SERVER['REMOTE_ADDR'];
			$v->activity = "asset uploaded";
			$v->note = "Asset $asset_rid uploaded (from".$_FILES['uploadfile']['name'].")";
			$v->asset_rid = $asset_rid;
			$v->save_activeversion();

		} else {
			$asset_rid = false;
		}

		@unlink($path.'.tmp');

		$_POST = $post_before;
		$_FILES = $files_before;

		return $asset_rid;
	}
	
	public function move_file($asset_rid, $tmp_asset) {
		// Permissions::user_can_modify_data();

		$asset = $this->_asset;
		
		if (sizeof($_FILES)) {
			if (PHP_SAPI != 'cli') {
				exec("mv \"" . $tmp_asset . "\" \"" . Kohana::config('core.assetpath').'/'.$asset->current_version->filename."\"");
			} else {
				exec("cp \"" . $tmp_asset . "\" \"" . Kohana::config('core.assetpath').'/'.$asset->current_version->filename."\"");
			}
			
			// FIXME: Check this worked! Fuck!
			
			if (!file_exists(Kohana::config('core.assetpath').'/'.$asset->current_version->filename)) {
				throw new Kohana_Exception('sledge.asset_could_not_be_moved', Kohana::config('core.assetpath'));
			}
			
			Asset::get_asset_preview($asset, 180, 200, 85, true, false, false);
			Asset::get_asset_preview($asset, 100, 100, 85, true, false, false);
		}
	}
	
	public function download() {
		$filename = preg_replace("/ |_/", "", basename($asset->filename));
		
		header('Content-Description: File Transfer');
		header('Content-Type: '. $this->_asset->asset_type->mimetype->current_version->mimetype);
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header('Content-Transfer-Encoding: binary');
		header('Pragma: public');
		header('Content-Length: ' . $this->_asset->current_version->filesize);
		$this->readfile();
		exit;
	}
	
	public function get_asset($asset_rid=false, $width=300, $height=300, $quality=85, $crop=false, $header=true, $download=false, $video_preview=false, $encoding=false) {
		if (!$asset_rid) {
			// Sick of getting exceptions from this stupid searchbot
			if (preg_match('/Dow Jones Searchbot/',@$_SERVER['HTTP_USER_AGENT'])) exit;
			// Fuck you GoogleBot
			if (preg_match('/Googlebot/',@$_SERVER['HTTP_USER_AGENT'])) exit;

			//throw new Kohana_Exception('sledge.missing_parameter', 'asset_rid');
		}
		$person = Kohana::instance()->person;
		if ($person->emailaddress == 'guest@hoopassociates.co.uk') {
			$asset = O::fa('asset')->where('visiblefrom_timestamp < now() AND ref_status_rid = 2')->find_by_rid((int) $asset_rid);
		} else {
			$asset = O::fa('asset')->find_by_rid((int) $asset_rid);
		}
		if ($asset->id == '') { return false; }
		
		Asset::get_asset_preview($asset, $width, $height, $quality, $crop, $header, $download, $video_preview, $encoding);
	}

	public function get_asset_json($asset_rid=0) {
		header("Pragma: no-cache");
		header("Cache-Control: no-store, no-cache, max-age=0, must-revalidate");
		header('Content-Type: text/x-json');
		$asset = O::fa('asset', (int)$asset_rid);
		$asset_array = array('rid' => $asset->rid, 'title' => $asset->title, 'description' => $asset->description, 'width' => $asset->width, 'height' => $asset->height, 'type' => $asset->get_mime_maintype());
		die(json_encode($asset_array));
	}


	public function web_get_asset($asset_rid, $width=false, $height=false, $quality=false, $crop=0, $header=true, $download=false, $template="default", $pre="", $post="") {

		if (!$width) $width = Kohana::config('core.asset_width');
		if (!$height) $height = Kohana::config('core.asset_width');
		if (!$quality) $quality = Kohana::config('core.asset_quality');

		$template_base = "site/subtpl_webgetasset_";
		if (class_exists('Cms_page_manager_Controller', FALSE)) {
			$webgetasset = O::fa('asset')->find_by_rid((int) $asset_rid);
		} else {
			$webgetasset = O::fa('asset')->where('visiblefrom_timestamp < now() AND ref_status_rid = 2')->find_by_rid((int) $asset_rid);
		}

		// are we calling this method via ajax URL? if so we want to echo and not return
		$return = (isset(Kohana::instance()->uri) and !strstr(Kohana::instance()->uri, 'web_get_asset'));

		if ($webgetasset->rid != '') {
			if ($template == "default") {
				$html = new View($template_base . $template . "_" . $webgetasset->get_mime_maintype(),array('webgetasset' => $webgetasset, 'width' => $width, 'height' => $height, 'quality' => $quality));
				if ($return) { return $html; } else { echo $html; }
			} else {
				$html = new View($template_base . $template, array('width' => $width, 'height' => $height, 'quality' => $quality));
				if ($return) { return $html; } else { echo $html; }
			} 
		} else {
			if (class_exists('Cms_page_manager_Controller',FALSE)) {
				return "<b>(<blink>*</blink> NO ASSET FOUND (rid: " . $asset_rid . ") <blink>*</blink>)</b>";
			} else {
				return "";
			}
		}
	}

	public function get_asset_preview($asset, $width, $height, $quality=85, $crop=false, $header=true, $download=false, $video_preview=false, $encoding=false) {
	
		if (ctype_digit($asset) || is_int($asset)) $asset = O::fa('asset',$asset);
		$sf = Kohana::config('core.assetpath').$asset->get_filename();
		
		if (!file_exists($sf)) { die("The source file ''$sf'' wasn't found, sorry."); }
		($download) and Asset::get_asset_download($asset);
	
		list($file, $ext) = explode('.', $asset->get_filename());
		if ($asset->get_mime_subtype() == 'pdf') {
			$ext = 'png';
			$mimetype = 'image/png';
		} elseif (($asset->get_mime_maintype() == 'video') and ($video_preview)) {
			if ($encoding) {
				if (preg_match('/\.flv$/',$encoding)) {
					$encoding = 'x-flv';
				}
				$asset_type = O::fa('asset_type')->find_by_name($encoding);
				if ($asset_type->rid) {
					$encoded = O::f('asset_encoding_v')->find_by_asset_rid_and_asset_type_rid($asset->rid,$asset_type->rid);
					if ($encoded->id) {
						header('Content-type: video/'.$asset_type->name);
						header("Content-Length: ".filesize(Kohana::config('core.assetpath').$encoded->get_filename()));
						readfile(Kohana::config('core.assetpath').$encoded->get_filename());
						exit;
					}
				}
			}
			header('Content-type: video/'.$asset->get_mime_subtype());
			header("Content-Length: ".filesize(Kohana::config('core.assetpath').$asset->get_filename())); # $asset->get_filename()));
			Asset::readfile($asset);
			exit;
		} elseif ($asset->get_mime_subtype() == 'x-flv' and !$video_preview) {
			# grab the last related asset of type image, if there is one, and set the mimetype
			$related_asset_exists = false;
			foreach (Relationship::find_partners('asset',$asset)->orderby('rel1.id', 'asc')->find_all() as $relatedasset) {
				# we need to only pull back assets that have been tagged with a video related tag

				# if this one exists and is an image (rather than a video or pdf for instance)
				if (($relatedasset->rid) and ($relatedasset->get_mime_maintype() == 'image')) {
					$related_asset_exists = true;
					$asset = $relatedasset;
					list($file, $ext) = explode('.', $asset->get_filename());
					$mimetype = $asset->get_mime_mimetype();
					break;
				}
			}

			# if there's no related asset that we can use, then try to get an asset based on the 'Default video related asset' tag
			if (!$related_asset_exists) {
				# is there an asset tagged 'default video related asset'
				$tag_default_video_related = O::fa('tag')->find_by_name('Default video related asset');
				if ($tag_default_video_related->rid) {
					$asset = Relationship::find_partner('asset',$tag_default_video_related)->find();
					if ($asset->rid) {
						list($file, $ext) = explode('.', $asset->get_filename());
						$mimetype = $asset->get_mime_mimetype();
					} else {
						return;
					}
				} else {
					return;
				}
			}
		}
		if (!isset($mimetype)) {
			//Log::add('MIME', 'anythingelse');
			$mimetype = $asset->get_mime_mimetype();
		}
		$crop_filename = $crop ? '_crop' : '';
		if ($crop) {
			$imagesize = @getimagesize(Kohana::config('core.assetpath').$asset->get_filename());
			// if it's an image
			if ($imagesize && count($imagesize)){

				list($image_width, $image_height) = $imagesize;

				// following code was ripped from: http://stackoverflow.com/questions/1679092/imagemagick-thumbnail-generation-with-php-using-crop#answer-1684980

				foreach (array($image_width,$image_height,$width,$height) as $val) {
					if (!ctype_digit($val) && !is_int($val)) exit;
				}

				$crop = '';
				if ($image_width / $image_height > $width / $height) {
					$crop .= ' -resize "x'.$height.'"';
					$resized_w = ($height / $image_height) * $image_width;
					$crop .= ' -crop "'.$width.'x'.$height.'+'.round(($resized_w - $width) / 2).'+0" +repage';
				} else {
					$crop .= ' -resize "' . $width . 'x"';
					$resized_h = ($width / $image_width) * $image_height;
					$crop .= ' -crop "'.$width.'x'.$height.'+0+'.round(($resized_h - $height) / 2).'" +repage';
				}

			} else {
				$crop = '-resize '.$width.'x'.$height.' -gravity center -crop '.$width.'x'.$height.'+0+0 +repage';
			}
			// the following would be ideal, but the ^ flag is only available in imagemagick version 6.3.8-3 or above
			// $crop = '-resize '.$width.'x'.$height.'^ -gravity center -extent '.$width.'x'.$height;
		} else {
			$crop = '-resize '.$width.'x'.$height.'\>';
		}
		$cachefile = Kohana::config('core.assetpath').'cache/'.$file.'_'.$width.'_'.$height.'_'.$quality.$crop_filename.'.'.$ext;
		if (!@file_exists($cachefile)) {
			if ($asset->get_mime_maintype() == 'image') {
				
				if (preg_match('/\.gif$/',Kohana::config('core.assetpath').$asset->get_filename())) {
					$exec = 'convert '.Kohana::config('core.assetpath').$asset->get_filename().' -coalesce '.$cachefile.'.tmp.gif';
					$result = shell_exec($exec);
					$exec2 = 'convert -quality '.$quality.' '.$crop.' '.$cachefile.'.tmp.gif '.$cachefile;
					$result2 = shell_exec($exec2);
					@unlink($cachefile.'.tmp.gif'); 
					if (!file_exists($cachefile)) {
						die("I tried to create a thumbnail, but was unable to write to the cache directory. ".$cachefile);
					}
				} else {
					$exec = ('convert -quality '.$quality.' '.$crop.' '.Kohana::config('core.assetpath').$asset->get_filename().' '.$cachefile);
					$result = exec($exec);
					if (!file_exists($cachefile)) {
						die("I tried to create a thumbnail, but was unable to write to the cache directory. ".$cachefile);
					}
				}
			} elseif ($asset->get_mime_maintype() == 'text') {
				$file = SLEDGEPATH . '/docroots/site/img/icons/40x40/txt_icon.gif';
			} elseif ($asset->get_mime_maintype() == 'application') {
				if ($asset->get_mime_subtype() == 'pdf') {
					exec('convert -quality '.$quality.' '.$crop.' '.Kohana::config('core.assetpath').$asset->get_filename().'[0] '.$cachefile);
				}
			}
		}
		if ($header && file_exists($cachefile)) {
			header('Content-type: '.$mimetype);
			header("Content-Length: ".filesize($cachefile));
			ob_clean(); flush(); readfile($cachefile);
			exit;
		} 
		return true;
	}

	public function get_upload_asset_tag_options($dont_die=false) {
		$assets = Tag::find_or_create_tag(1,'Assets');
		$smartfolders = Tag::find_or_create_tag($assets->rid,'Smart folders',false,false,true);
		$smart_tag_rids = array_merge(array($smartfolders->rid),Tag::get_descendanttags($smartfolders->rid));

		$tag_rids = array();
		foreach (Tag::get_descendanttags($assets->rid) as $tag_rid) {
			if (!in_array($tag_rid, $smart_tag_rids)) {
				$tag_rids[] = $tag_rid;
			}
		}
		$tags = array();
		foreach (O::fa('tag')->where("rid in (".implode(',',$tag_rids).")")->orderby('name','asc')->find_all() as $tag) {
			$tags[$tag->parent_rid][] = $tag;
		}
		echo '<option value="">- Please select -</option>';
		Asset::recurse_get_upload_asset_tag_options($tags, $assets->rid);
		if (!$dont_die) exit;
	}

	public function recurse_get_upload_asset_tag_options($tags, $parent_rid, $depth=0) {
		foreach ($tags[$parent_rid] as $tag) {?>
			<option value="<?=$tag->rid?>"><?for ($i=0; $i<($depth*3);$i++) echo '&nbsp;';?><?=$tag->name?></option>
			<?if (isset($tags[$tag->rid])) {?>
				<?Asset::recurse_get_upload_asset_tag_options($tags, $tag->rid, $depth+1);?>
			<?}?>
		<?}
	}

	public function get_previous_next_attributes($asset_rid, $tag_rid, $sortby, $order) {
		if ($tag_rid == 'NULL') $tag_rid=false;
		if ($sortby == 'NULL') $sortby=false;
		if ($order == 'NULL') $order=false;

		$params = new stdClass;

		if (!$asset_rid) {
			throw new Kohana_Exception('sledge.missing_parameter', 'asset_rid');
		}

		$params->asset = O::fa('asset')->where("asset_v.rid = $asset_rid")->select('asset_v.*, asset.sequence')->find();
		if (!$params->asset->rid) {
			throw new Kohana_Exception('sledge.asset_not_found', $asset_rid);
		}

		if ($tag_rid == 'undefined') {
			$tag_rid = false;
		}

		if ($tag_rid) {
			$params->tag = O::fa('tag',$tag_rid);
			if (!$params->tag->rid) {
				throw new Kohana_Exception('sledge.tag_not_found', $tag_rid);
			}

			if (!$sortby) {
				switch ($params->tag->item_ordering_policy_rid) {
					case 1:
						$params->sortby = 'sequence';
						break;
					case 2:
						$params->sortby = 'title';
						break;
					case 3:
					default:
						$params->sortby = 'audit_time';
						break;
				}
			} else {
				$params->sortby = $sortby;
			}

			if (!$order) {
				switch ($params->tag->item_ordering_direction) {
					case 1:
						$params->order = 'asc';
						break;
					case 2:
					default:
						$params->order = 'desc';
						break;
				}
			} else {
				$params->order = $order;
			}
		} else {
			$params->sortby = $sortby;
			$params->order = $order;

			if (!$params->sortby) $params->sortby = 'audit_time';
			if (!$params->order) $params->order = 'desc';
		}

		return $params;
	}

	public function get_previous_asset($asset_rid, $tag_rid, $sortby, $order, $count=false, $return=false) {
		$params = Asset::get_previous_next_attributes($asset_rid, $tag_rid, $sortby, $order);
		return Asset::next_in_sequence($params, $count, $return);
	}

	public function next_in_sequence($params, $count=false, $return=false) {
		$assets = Tag::find_or_create_tag(1,'Assets');
		$smart_folders = Tag::find_or_create_tag($assets->rid,'Smart folders',false,false,true);
		$uploaded_by = Tag::find_or_create_tag($smart_folders->rid, 'Uploaded by');

		$smart_folder_rids = Tag::get_descendanttags($smart_folders->rid);

		if (isset($params->tag)) {
			if (in_array($params->tag->rid, $smart_folder_rids)) {
				$model = Asset::get_smartfolder_model($params->tag);
			} else {
				$model = Relationship::find_partner('asset',$params->tag);
			}
		} else {
			$model = O::fa('asset');
		}

		$table = ($params->sortby == 'sequence' ? 'asset' : 'asset_v');

		if ($params->sortby == 'relevance') $params->sortby = 'audit_time';

		if ($params->sortby == 'audit_time' || $params->sortby == 'title') {
			$asset_value = "'".$params->asset->{$params->sortby}."'";
		} else {
			$asset_value = $params->asset->{$params->sortby};
		}

		if ($params->order == 'asc') {
			if ($count) {
				return $model->where($table.'.'.$params->sortby.' < '.$asset_value)->find_all()->count();
			}
			$rid = $model->where($table.'.'.$params->sortby.' < '.$asset_value)->orderby($table.'.'.$params->sortby,'desc')->limit(1)->find()->rid;
			if ($return) return $rid;
			die((string)$rid);
		} else {
			if ($count) {
				return $model->where($table.'.'.$params->sortby.' > '.$asset_value)->find_all()->count();
			}
			$rid = $model->where($table.'.'.$params->sortby.' > '.$asset_value)->orderby($table.'.'.$params->sortby,'asc')->limit(1)->find()->rid;
			if ($return) return $rid;
			die((string)$rid);
		}

		if ($return) return 0;
		die((string)0);
	}

	public function get_next_asset($asset_rid, $tag_rid, $sortby, $order, $count=false, $return=false) {
		$params = Asset::get_previous_next_attributes($asset_rid, $tag_rid, $sortby, $order);
		$params->order = ($params->order == 'asc') ? 'desc' : 'asc';
		return Asset::next_in_sequence($params, $count, $return);
	}

	public function get_saveasset_token($return=false) {
		if (!isset($_GET['status'])) throw new Kohana_Exception('sledge.missing_parameter','status');
		$token = (string)new Token('saveasset',false,'0:'.$_GET['status'],false);
		
		if ($return) {
			return $token;
		} else {
			echo $token;
			exit;
		}
	}

	public function import_assets_from_dir($dir, $tag_rid='', $person_rid_or_email, $quiet=false) {
		$ki = Kohana::Instance();
		$rc = RequestCache::Instance();

		$person_before = $ki->person;

		$tag = O::fa('tag',$tag_rid);

		if ($ki->person->rid != $person_rid_or_email) {
			if (ctype_digit($person_rid_or_email) || is_int($person_rid_or_email)) {
				$ki->person = O::fa('person',$person_rid_or_email);
			} else {
				$ki->person = O::fa('person')->find_by_emailaddress($person_rid_or_email);
			}
		}

		if (!$dh = opendir($dir)) {
			if ($quiet) return false;
			die("Error opening directory: $dir\n");
		}

		while ($file = readdir($dh)) {
			if ($file != '.' && $file != '..' && !preg_match('/\.tmp$/',$file) && $file != '__MACOSX') {
				if (is_file($dir.'/'.$file)) {
					if (!$quiet) {
						echo "$file ... ";
						ob_flush();
					}

					$rc->assets_uploaded_total++;

					if ($asset_rid = Asset::import_asset($dir.'/'.$file, false, $tag)) {
						$rc->assets_uploaded_ok++;
					}

					if (!$quiet) {
						echo $asset_rid."\n";
						ob_flush();
					}
				} else if (is_dir($dir.'/'.$file)) {
					if ($tag->rid) {
						$new_tag = Tag::find_or_create_tag($tag->rid,$file);
						Asset::import_assets_from_dir($dir.'/'.$file, $new_tag->rid, $person_rid_or_email, $quiet);
					} else {
						Asset::import_assets_from_dir($dir.'/'.$file, $tag_rid, $person_rid_or_email, $quiet);
					}
				}
			}
		}

		closedir($dh);
		$ki->person = $person_before;
		return true;
	}



	public function allowed_to_upload($uploadfile, $asset_rid=NULL) {
		//Permissions::user_can_modify_data();

		// check the file has actually been uploaded
		if ($uploadfile['error'] !== UPLOAD_ERR_OK or $uploadfile['size'] == 0) {
			$this->errors[] = 'There was an error uploading the file, please try again.';
			@unlink($uploadfile['tmp_name']);
			return false;
		}
		// get the mimetype of the file 
		if (trim(`uname`) == 'Darwin') {
			exec('/opt/local/bin/file -bi "'.$uploadfile['tmp_name'] . '"', $file_command_says);
		} else {
			exec('file -bi "'.$uploadfile['tmp_name'] . '"', $file_command_says);
		}

		list($main_mimetype, $sub_mimetype) = explode('/', preg_replace("/;.*/", '', $file_command_says[0]));

		// check that this type of asset is allowed to be uploaded (all the way up the asset type tree)
		return Asset::is_allowed_assettype($sub_mimetype);
	}

	public function is_allowed_assettype($sub_mimetype) {
		$assettype = O::fa('asset_type')->find_by_name($sub_mimetype);

		if ((!$assettype->id)or($assettype->allowed_to_upload === 'f' || $assettype->allowed_to_upload === 0)) {
			$this->errors[] = 'The filetype ('.$sub_mimetype.') you are trying to upload is not allowed. Terribly sorry.';
			return false;
		}


		while ($assettype->parent_rid != '') {
			$assettype = O::fa('asset_type')->find_by_rid($assettype->parent_rid);
			if ($assettype->allowed_to_upload === 'f' || $assettype->allowed_to_upload === 0) {
				$this->errors[] = 'The filetype you are trying to upload is not allowed. Terribly sorry.';
				return false;
			}
		}
		return true;
	}

	public function put_asset_data($uploadfile, $asset_rid=0, $replacing=false, $new_asset = false) {
		# PLEASE NOTE: for printsite, users of the dashboard should be able to upload assets
		// Permissions::user_can_modify_data();

		if (!sizeof($_POST) and (PHP_SAPI != 'cli')) {
			throw new Kohana_Exception('sledge.null_post_received');
		}

		# Validate metadata
		$metadata = Tag::find_or_create_tag(1,'Metadata');
		$assets = Tag::find_or_create_tag($metadata->rid,'Assets');

		$errors = array();
		$metadata = array();

		if (!$new_asset && !$replacing) {
			foreach (Relationship::find_partners('key_restrictions_v',$assets)->find_all() as $mkr) {
				if (!isset($_POST['metadata_'.$assets->rid.'_'.$mkr->key_name]) || !@$_POST['metadata_'.$assets->rid.'_'.$mkr->key_name]) {
					if ($mkr->required == 't') {
						$errors['metadata_'.$assets->rid.'_'.$mkr->key_name] = 'This field is required';
					} else {
						continue;
					}
				}

				if (isset($_POST['metadata_'.$assets->rid.'_'.$mkr->key_name])) {
					if ($mkr->value_restrictions) {
						file_put_contents("/tmp/debug",print_r($mkr,true));
						if (!preg_match($mkr->value_restrictions, $_POST['metadata_'.$assets->rid.'_'.$mkr->key_name])) {
							$errors['metadata_'.$assets->rid.'_'.$mkr->key_name] = 'Invalid characters in input, did not match ' . $mkr->value_restrictions;
							continue;
						}
					}

					$metadata[$mkr->key_name] = $_POST['metadata_'.$assets->rid.'_'.$mkr->key_name];
				}
			}

			if (!empty($errors)) {
				die(json_encode($errors));
			}
		}

		$asset_v = O::fa('asset',$asset_rid);

		if ($asset_v->rid) {
			$keep_audit_person = $asset_v->audit_person;
		}

		if ($asset_rid == 0 || $replacing) {
			list($asset_v->width, $asset_v->height,,) = getimagesize($uploadfile['tmp_name']);
			$asset_v->filesize = filesize($uploadfile['tmp_name']);

			if (trim(`uname`) == 'Darwin') {
				exec('/opt/local/bin/file -bi "'.$uploadfile['tmp_name'].'"', $file_command_says);
			} else {
				exec('file -bi "'.$uploadfile['tmp_name'].'"', $file_command_says);
			}

			if (!isset($file_command_says[0])) {
				throw new Kohana_Exception('sledge.unable_to_process_tmp_file', 'file -bi "'.$uploadfile['tmp_name'].'"');
			}

			list($main_mimetype, $sub_mimetype) = explode('/', preg_replace("/;.*/", '', $file_command_says[0]));
			$_POST['file_command_says'] = $file_command_says[0];
			$asset_v->filename = substr($uploadfile['name'], 0, 254);
			$_POST['title'] = substr($_POST['title'], 0, 254);
			$_POST['asset_type_rid'] = O::fa('asset_type')->find_by_name($sub_mimetype)->rid;  // $allowedmimetypes[$main_mimetype][$sub_mimetype];
			$_POST['ref_status_rid'] = isset($_POST['ref_status_rid']) ? $_POST['ref_status_rid'] : 1;
		}
		if ((isset($uploadfile))and(!is_null($uploadfile))) {
			$_POST['filename'] = $asset_v->get_filename_noextension() . "." . strtolower($asset_v->get_filename_extension());
		}

		// pass false to quicksave to load $_POST into the object without saving to the db
		$asset_v->quicksave(false);
		$asset_v->save_activeversion();

		$metadata_tag = Tag::find_or_create_tag(1, 'Metadata');
		$asset_metadata = Tag::find_or_create_tag($metadata_tag->rid, 'Assets');

		foreach ($metadata as $key => $value) {
			$met = O::f('metadata_v')->join('relationship_partner as r1','r1.item_rid','metadata_v.id')->join('relationship_partner as r2','r1.relationship_id','r2.relationship_id')->where("r1.item_tablename = 'metadata_v' and r2.item_tablename='tag' and r2.item_rid = $assets->rid and metadata_v.item_tablename='asset' and metadata_v.item_rid=$asset_v->rid and metadata_v.key='$key'")->find();
			if ($met->id) {
				$met->value = $value;
				$met->save();
			} else {
				$met = O::f('metadata_v');
				$met->item_tablename = 'asset';
				$met->item_rid = $asset_v->rid;
				$met->key = $key;
				$met->value = $value;
				$met->save();

				Relationship::create_relationship(array('asset','tag'),array($asset_v->rid,$asset_metadata->rid));
			}
		}

		if (isset($keep_audit_person)) {
			O::q("update asset_v set audit_person = '$keep_audit_person' where rid = $asset_v->rid");
		}

		return $asset_v->rid;
	}
	
}