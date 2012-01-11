<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates		www.thisishoop.com	 mail@hoopassociates.co.uk
?>

<?
if (@$_REQUEST['modal'] == 'true'){
	// FIXME
	require 'get-tag-items-modal.php';
	exit;
}

if (!isset($this->basetag) and isset($_REQUEST['basetag_rid'])) {
	$this->basetag = O::fa('tag', $_REQUEST['basetag_rid']);
}
if(isset($this->sr)) {
	$find = $this->sr;
	$this->tag = O::fa('tag');
	$this->tag->name = 'Search Results';
} else {
	$find = Tag::find($this->tag_rid, $this->basetag_rid, $this->page, 30);
}

if (isset($this->tag_rid)) {
	$this_tag = O::fa('tag',$this->tag_rid);
	$rubbish_view = (Tag::is_smart('tag',$this->tag_rid) && $this_tag->name == 'Rubbish') ? true : false;
} else {
	$rubbish_view = false;
}

?>

<div class="sledge-tabs">
	<div class="ui-helper-right" style="padding: .4em .6em 0 0;">
		<select id="sledge-tagmanager-sortby-select" class="sledge-selectmenu ui-helper-left" style="width: 98px">
			<optgroup label="Sort">
				<option value="audit_time"<?if (@$this->sortby == 'audit_time') {?> selected="selected"<?}?>>Date</option>
				<option value="title"<?if (@$this->sortby == 'title') {?> selected="selected"<?}?>>Title</option>
			</optgroup>
		</select>
		<select id="sledge-tagmanager-order-select" class="sledge-selectmenu ui-helper-left" style="width: 130px">
			<optgroup label="Order">
				<option value="desc"<?if (strtolower(@$this->order) == 'desc') {?> selected="selected"<?}?>>Descending</option>
				<option value="asc"<?if (strtolower(@$this->order) == 'asc') {?> selected="selected"<?}?>>Ascending</option>
			</optgroup>
		</select>
	</div>
	<ul>
		<li><a href="#sledge-tagmanager-view-list">List</a></li>
		<li><a href="#sledge-tagmanager-view-thumbnails">Thumbnails</a></li>
	</ul>
	<div id="sledge-tagmanager-view-list" class="sledge-tagmanager-list">
		<table width="100%" border="1" class="sledge-tagmanager-table-list">
			<thead>
				<tr>
					<td colspan="3" style="padding:0"><h3><?=$this->tag->name?></h3></td>
				</tr>
			</thead>
			<tbody>
				<?
				$total = 0;
				foreach ($find as $item) {

					if ($this->basetag->name == 'Assets') {
						$total += $item->filesize;
					}

					if (isset($item->item_tablename) && @$item->item_tablename) {
						$table = $item->item_tablename;
					} else if (isset($item->tablename) && @$item->tablename) {
						$table = $item->tablename;
					} else {
						$table = preg_replace('/_v_model$/','',strtolower(get_class($item)));
					}

					echo new View('cms/ui/subtpl_tag_appliedto_list_row_' . $table, array('item'=>$item));
				}?>
			</tbody>
		</table>
	</div>
	<div id="sledge-tagmanager-view-thumbnails">
		<h3>
			<?=$this->tag->name?>
		</h3>
		<div class="sledge-tagmanager-assets sledge-tagmanager-thumbs ui-helper-clearfix">
			<?foreach ($find as $item) {
				if (isset($item->item_tablename) && @$item->item_tablename) {
					$table = $item->item_tablename;
				} else if (isset($item->tablename) && @$item->tablename) {
					$table = $item->tablename;
				} else {
					$table = preg_replace('/_v_model$/','',strtolower(get_class($item)));
				}

				echo new View('cms/ui/subtpl_tag_appliedto_thumb_row_' . $table, array('item'=>$item));
			}?>
		</div>
	</div>
	<div style="padding: .5em 0 .5em .5em;border-color:#ccc;border-width:1px 0 0 0;" class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
		<div class="ui-helper-right" style="margin: .5em .5em 0 0">
			Total files: <?=count($find)?>
			<?if ($this->basetag->name == 'Assets') {?>
				| Total size: <?=Misc::format_filesize($total)?>
			<?}?>
		</div>
		<div id="sledge-tagmanager-checkactions" class="ui-widget-content">
			With <span id="sledge-tagmanager-amount-checked"></span> selected:
		</div>
		<div id="sledge-tagmanager-multiactons" class="ui-widget-content">
			<button id="sledge-button-multiaction-edit" disabled="disabled" class="sledge-button ui-button-text-icon">
				<span class="ui-button-icon-primary ui-icon ui-icon-wrench"></span>
				View/Edit
			</button>
			<button id="sledge-button-multiaction-delete" disabled="disabled" class="sledge-button ui-button-text-icon">
				<span class="ui-button-icon-primary ui-icon ui-icon-circle-close"></span>
				Delete
			</button>
			<?if (@$_REQUEST['tagmanager'] == 'assets') {?>
				<button id="sledge-button-multiaction-download" disabled="disabled" class="sledge-button ui-button-text-icon">
					<span class="ui-button-icon-primary ui-icon ui-icon-arrowreturn-1-s"></span>
					Download
				</button>
			<?}?>
		</div>
	</div>
</div>
<?	

/*

// search results
if(isset($this->sr)) {
	$find = $this->sr;
	$this->tag = O::fa('tag');
	$this->tag->name = 'Search Results';
} else {
	$find = Tag::find($this->tag_rid, $this->basetag, $this->page, 30);
}

if (isset($this->tag_rid)) {
	$this_tag = O::fa('tag',$this->tag_rid);
	$rubbish_view = (Tag::is_smart('tag',$this->tag_rid) && $this_tag->name == 'Rubbish') ? true : false;
} else {
	$rubbish_view = false;
}

?>
<div class="header">
	<small class="right" style="margin-right:5px;margin-top:-1px">
		<select name="sortby" id="change_sortby" style="padding:0px">
			<optgroup label="Sort by">
				<option value="audit_time"<?=($this->sortby=='audit_time')?' selected':'';?>>Date</option>
				<?if ($this->sortby == 'subject'){?>
					<option value="subject" selected>Subject</option>
				<?} else {?>
					<option value="title"<?=($this->sortby=='title')?' selected':'';?>>Name</option>
				<?}?>
			</optgroup>
		</select>
		&nbsp;
		<select name="order" id="change_order" style="padding:0px">
			<optgroup label="Order">
				<option value="asc"<?=(@$this->order=='asc')?' selected':'';?>>Ascending</option>
				<option value="desc"<?=(@$this->order=='desc')?' selected':'';?>>Descending</option>
			</optgroup>
		</select>
		&nbsp;
		<select name="view" id="change_view" style="padding:0px">
			<optgroup label="View as">
				<option value="list">List</option>
				<option value="thumb">Thumbnails</option>
			</optgroup>
		</select>
	</small>
	<div class="col1 check"><input type="checkbox" id="checkall" /></div>
	<div class="col3">
		<?if ($this->tag->rid && ((Tag::has_ancestor_smarttag($this->tag->rid) or Tag::has_ancestor_protectedtag($this->tag->rid))) and @$_REQUEST['tagmanager'] != 'tags' and !preg_match('/@hoopassociates\.co\.uk$/',$this->person->emailaddress)){?>
			<strong class="tag"><?=$this->tag->name?></strong>
		<?} else {?>
			<a href="#tagedit/<?=$this->tag->rid;?>" id="tagedit_<?=$this->tag->rid;?>" class="editfolder"><?=$this->tag->name?>
				&nbsp;
				(
				<span style="font-weight:normal;color:red">
					Edit group
				</span>
				)
			</a>
		<?}?>
		&nbsp;&nbsp;(<?=$this->nr?> items)
	</div>
</div>

<?
	if (!function_exists('recurse2')) {
		function recurse2($tag_contents, $c, $last=false, $rubbish_view = false) {
			$ki = Kohana::Instance();
			$tablename = (isset($tag_contents->item_tablename))?$tag_contents->item_tablename:$tag_contents->tablename;
			if (!$tablename) $tablename = preg_replace('/_v$/','',$tag_contents->table);
			$ki->type = $tablename;
			$ki->recursing = array();
			$ki->recursing['tag_contents'] = O::fa($tablename)->rubbish($rubbish_view)->find_by_rid($tag_contents->rid);
			$ki->recursing['c'] = $c;
			$ki->recursing['last'] = $last;
			// $item = O::f($tablename)->rubbish($rubbish_view)->find_by_id($tag_contents->rid);
			$item = $ki->recursing['tag_contents'];
			$ki->recursing["rubbishrow"] = false;
			// we don't want rubbish rows showing with normal rows anymore 
			/* if ($ki->recursing['rubbishrow']) {
				return;
			}*/
/*
			if (!$ki->recursing['tag_contents']->id) {
				return false;
			}
			$rowtype = (strstr($_SERVER['SCRIPT_URI'],'cms_product_manager')!=false)?'product':$tablename;
			?>
			<li class="<?if($ki->recursing['rubbishrow']){?>rubbishrow <?}?><?if ($last and $ki->pagination->total_pages > 1) {?>last<?}?>">
				<div class="row message"> <? /*<?=$tag_contents->tablename;?>">*/ ?>
				<?
/*					$ki->type = $rowtype;
					$ki->recursing["rowtype"] = $rowtype;

					if ($rowtype == 'stuff') {
						// What kind of stuff is this?
						$tag_v = Relationship::find_partner('tag',$ki->recursing['tag_contents'])->find();

						if (!$tag_v->rid) {
							die("Could not find tag for stuff row ".$ki->recursing['tag_contents']->rid);
						}
						$rowtype = strtolower(str_replace(' ','_',$tag_v->name));
					}

					#i think this should be commented out, looks legacy -mark
					#$rowtype = preg_replace("/cms_person/", "person", $rowtype);
					echo new View('cms/subtpl_tag_appliedto_'.$ki->rowview.'_row_' . $rowtype); 
				?>
				</div>
			</li>
		<?}
	}
	?>
	<?
		if (sizeof($find)) {
			?><ul class="assets tree"><?
			$c = 1;
			foreach($find as $tag_contents) {
				$this->recursion_count = 1;
				recurse2($tag_contents, $this->recursion_count, $c == sizeof($find), $rubbish_view);
				$c++;
			}
			?></ul>
			<script type="text/javascript">
			$(".tags").tooltip({
				track: true,
				delay: 800,
				showURL: false,
				showBody: " - "
			});
			</script><?
		} 
		if ($this->pagination->total_pages > 1) {
			echo $this->pagination->create_links();
		}*/
	?>
