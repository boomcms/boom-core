<?
	$c = isset($this->c) ? $this->c : 0;

	$asset_type = O::fa('asset_type', O::fa('asset_type', $this->item->asset_type_rid)->parent_rid)->name;
	if ($asset_type == 'application') {
		$asset_type = 'document'; // bit of a hack, but a useful one
	}

	$tags = array();
	foreach (Relationship::find_partners('tag', $this->item)->find_all() as $tag) {
		$tags[] = $tag->rid;
	}

	$first_version = O::f('asset_v')->where("asset_v.rid = {$this->item->rid}")->orderby('audit_time', 'ASC')->limit(1)->find();
	$uploader = O::fa('person', $first_version->audit_person);
?>

<div id="sledge-asset-detailview">

	<form onsubmit="return false;">

		<input type="hidden" name="rid" value="<?= $this->item->rid?>" />
		<input type="hidden" name="tags" value="<?=implode(',', $tags)?>" />
		<input type="hidden" name="old_visiblefrom_timestamp" id="old_visiblefrom_timestamp" value="<?=date("Y-m-d H:i:s", $this->item->visiblefrom_timestamp)?>" />
		<input type="hidden" name="old_ref_status_rid" id="old_ref_status_rid" value="<?=$this->item->ref_status_rid?>" />

		<div class="sledge-tabs ui-helper-clearfix">

			<ul>
				<li><a href="#sledge-asset-detailview-attributes<?=$c;?>">Attributes</a></li>
				<li><a href="#sledge-asset-detailview-info<?=$c;?>">Info</a></li>
				<li><a href="#sledge-asset-detailview-metadata<?=$c;?>">Metadata</a></li>
				<li><a href="#sledge-asset-detailview-folders<?=$c;?>">Folders</a></li>
				<li><a href="#sledge-asset-detailview-relationships<?=$c;?>">Asset relationships</a></li>
			</ul>

			<div class="ui-tabs-panel ui-widget-content ui-helper-left">

				<a href="/_ajax/call/asset/get_asset/<?= $this->item->rid?>/600/500" 
					title="<?= $this->item->title?>" 
					title="Click for larger view" 
					class="ui-helper-left sledge-asset-preview">
					<img class="ui-state-active ui-corner-all" src="/_ajax/call/asset/get_asset/<?= $this->item->rid?>/160">
				</a>

			</div>

			<div id="sledge-asset-detailview-attributes<?=$c;?>" class="ui-helper-left">
				<table>
					<tr>
						<td>Title</td>
						<td>
							<input type="text" name="title" class="sledge-input" value="<?= $this->item->title?>" />
						</td>
					</tr>
					<tr>
						<td>Filename</td>
						<td>
							<input type="text" name="filename" class="sledge-input" value="<?= $this->item->filename?>" />
						</td>
					</tr>
					<tr>
						<td style="vertical-align: top">Description</td>
						<td>
							<textarea name="description" class="sledge-textarea"><?= $this->item->description?></textarea>
						</td>
					</tr>
					<tr>
						<td>Status</td>
						<td>
							<select name="ref_status_rid">
							<?
								$refpagestatus = O::fa('ref_status')->find_all();
								foreach ($refpagestatus as $status) {
									?><option value="<?=$status->rid?>"<?= ($status->rid == $this->item->ref_status_rid ? ' selected="selected"':'')?>><?=$status->name?></option><?
								}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Visible from</td>
						<td>
							<input type="text" name="visiblefrom_timestamp" class="sledge-datepicker sledge-input" value="<?= date('d F Y', $this->item->visiblefrom_timestamp);?>" />
						</td>
					</tr>
				</table>
			</div>

			<div id="sledge-asset-detailview-info<?=$c;?>" class="ui-helper-left">

				<table width="100%">
					<tr>
						<td width="100">Type</td>
						<td><?= ucfirst($asset_type);?></td>
					</tr>
					<tr>
						<td>Filesize</td>
						<td><?= Misc::format_filesize($this->item->filesize) ?></td>
					</tr>
					<?if ($asset_type == 'image') {?>
					<tr>
						<td>Dimensions</td>
						<td><?=$this->item->width?> x <?=$this->item->height?></td>
					</tr>
					<?}?>
					<tr>
						<td>Uploaded by</td>
						<td><?= $uploader->firstname.' '.$uploader->lastname?></td>
					</tr>
					<tr>
						<td>Upload on</td>
						<td><?= date('d F Y h:i:s', strtotime($first_version->audit_time))?></td>
					</tr>
					<tr>
						<td>Versions</td>
						<td>This asset has been updated <?= count(O::f('asset_v')->find_all_by_rid($this->item->rid)) ?> times.</td>
					</tr>
				</table>

			</div>

			<div id="sledge-asset-detailview-metadata<?=$c;?>" class="ui-helper-left">
				metadata
			</div>

			<div id="sledge-asset-detailview-folders<?=$c;?>" class="ui-helper-left">
			</div>

			<div id="sledge-asset-detailview-relationships<?=$c;?>" class="ui-helper-left">
				relationships
			</div>

			<br class="ui-helper-clear" />

			<div style="padding: .8em 0 .8em .8em;border-color:#ccc;border-width:1px 0 0 0;" class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
				<button class="sledge-button ui-button-text-icon sledge-tagmanager-asset-save" rel="<?=$this->item->rid?>">
					<span class="ui-button-icon-primary ui-icon ui-icon-disk"></span>
					Save
				</button>
				<button class="sledge-button ui-button-text-icon sledge-tagmanager-asset-delete" rel="<?=$this->item->rid?>">
					<span class="ui-button-icon-primary ui-icon ui-icon-circle-close"></span>
					Delete
				</button>
				<button class="sledge-button ui-button-text-icon sledge-tagmanager-asset-download" rel="<?=$this->item->rid?>">
					<span class="ui-button-icon-primary ui-icon ui-icon-arrowreturn-1-s"></span>
					Download
				</button>
			</div>
		</div>
	
	</form>

</div>
