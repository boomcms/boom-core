<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<div class="header">
	<div class="col3 actions">
		<a href="javascript:history.back(-1)" class="back">Back</a>
	</div>
	<div class="col3">
		<span class="right">
			<?=date("d M Y", strtotime($this->item->audit_time))?>
		</span>
		<?=htmlentities($this->item->title)?>
	</div>
</div>

<? if ($this->item->id == '') {
	die('<span class="errorimg" style="margin:20px">Asset not found</span>');
} else {
	$asset_type = O::fa('asset_type', O::fa('asset_type', $this->item->asset_type_rid)->parent_rid)->name;
	$asset_sub_type = O::fa('asset_type', $this->item->asset_type_rid)->name;
	if ($asset_type == 'application') {$asset_type = 'document';} // bit of a hack, but a useful one

	$assignedtags_str = '';
	$assignedtags_html_str = '';
	foreach (Relationship::find_partners('tag',$this->item)->find_all() as $tag) {
		if (Tag::has_ancestortag($tag->rid, $this->basetag)) {
			$assignedtags_str .= $tag->rid;
			$assignedtags_html_str .= '<a href="#tag/'.$tag->rid.'" rel="ajax">'.$tag->name.'</a> | ';
		}
	}
	$assignedtags_html_str = rtrim($assignedtags_html_str, ' | ');
?>
<div class="detailview">
	<fieldset>
		<?if (preg_match("/flv/", $this->item->file_command_says)) {?>
			<script type="text/javascript" src="/sledge/js/swfobject.js"></script>
			<div id="detailview-player">
				<p class="error">
					To view video, you need to have the Flash plugin installed. To get Flash, please visit the <a href="http://www.adobe.com/go/getflashplayer">Adobe website</a>.
				</p>
			</div>
			<script type="text/javascript">
				var so = new SWFObject('/sledge/img/player.swf','mpl','300','200','9');
				so.addParam('allowscriptaccess','always');
				so.addParam('allowfullscreen','true');
				so.addParam('flashvars','&stretching=exactfit&image=/_ajax/call/asset/get_asset/<?=$this->item->rid?>/300/200/85/1&file=/_ajax/call/asset/get_asset/<?=$this->item->rid?>/NULL/NULL/NULL/NULL/1/0/1/null.flv');
				so.write('detailview-player');
			</script>
		<?} else {?>
			<a href="/_ajax/call/asset/get_asset/<?=$this->item->rid?>/550/550/85/1/1/0/0/1/null.jpg" class="thumbpreview thickbox2" title="<?=htmlspecialchars($this->item->title);?>">
				<img src="/_ajax/call/asset/get_asset/<?=$this->item->rid?>" style="margin:0px 15px" />
			</a>
		<?}?>
	</fieldset>
	<fieldset>
		<legend>Basic information</legend>
		<div style="margin:5px 10px">
			<table border="0" cellpadding="0" cellspacing="0" class="basic_information">
				<tr>
					<td>Type:</td>
					<td><?=ucfirst($asset_type);?> / <?=strtoupper($asset_sub_type);?></td>
				</tr>
				<tr>
					<td>Filename:</td>
					<td width="100%"><?=$this->item->filename?></td>
				</tr>
				<tr>
					<td>Filesize:</td>
					<td><?=$this->item->get_filesize();?></td>
				</tr>
				<?if ($asset_type == 'image') {?>
					<tr>
						<td>Dimensions:</td>
						<td><?=$this->item->width?> x <?=$this->item->height?></td>
					</tr>
				<?}?>
				<?if (trim($this->item->description) != '') {?>
				<tr>
					<td>Description:</td>
					<td><?=$this->item->description?></td>
				</tr>
				<?}?>
				<tr>
					<td>Tags:</td>	
					<td><small><?=$assignedtags_html_str;?></small></td>
				</tr>
				<tr>
					<td colspan="2" style="padding-top:12px">
						<input type="button" value="Download" onclick="window.location='/_ajax/call/asset/get_asset/<?=$this->item->rid?>/0/0/0/0/0/1';" />
					</td>
				</tr>
			</table>
		</div>
	</fieldset>
</div>
<?}?>
