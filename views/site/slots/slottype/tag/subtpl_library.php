<?
$toplevel_tag_rid = isset($_REQUEST['toplevel_tag']) ? $_REQUEST['toplevel_tag'] : $toplevel_rid;
$page = (isset($this->page) and isset($this->page->rid)) ? $this->page : O::f('site_page')->get_homepage();

if ($this->page_model == 'cms_page') {?>
	<div class="rel-toplevel-<?=$toplevel_tag_rid;?> cms-tag-library">
		Asset library for tag: <strong><?=$target->name;?></strong>
	</div>
<?} else {?>
	<div class="rel-toplevel-<?=$toplevel_tag_rid;?>">
	<?
		echo Tag::get_tag_manager('tag_manager', 'site_tag_manager', $target, $target, array('asset'), 'list', 'date');
	?>
	</div>
<?}?>
