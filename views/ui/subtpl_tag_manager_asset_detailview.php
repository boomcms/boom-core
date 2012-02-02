<div id="sledge-asset-detailview">

	<form action="/asset/save/<?= $asset->id?>" method='post' id="asset-save" <? //onsubmit="return false;"?>>

		<input type="hidden" name="id" id="asset_id" value="<?= $asset->id?>" />
		<input type="hidden" name="tags" value="<?=implode(',', $asset->tags())?>" />
		<input type="hidden" name="old_visiblefrom_timestamp" id="old_visiblefrom_timestamp" value="<?=date("Y-m-d H:i:s", $asset->visible_from)?>" />
		<input type="hidden" name="old_ref_status_rid" id="old_ref_status_rid" value="<?=$asset->status?>" />

		<div class="sledge-tabs ui-helper-clearfix">

			<ul>
				<li><a href="#sledge-asset-detailview-attributes<?=$asset->id;?>">Attributes</a></li>
				<li><a href="#sledge-asset-detailview-info<?=$asset->id;?>">Info</a></li>
				<li><a href="#sledge-asset-detailview-metadata<?=$asset->id;?>">Metadata</a></li>
				<li><a href="#sledge-asset-detailview-folders<?=$asset->id;?>">Folders</a></li>
				<li><a href="#sledge-asset-detailview-relationships<?=$asset->id;?>">Asset relationships</a></li>
			</ul>

			<div class="ui-tabs-panel ui-widget-content ui-helper-left">

				<a href="/asset/view/<?= $asset->id?>/600/500" 
					title="<?= $asset->title?>" 
					title="Click for larger view" 
					class="ui-helper-left sledge-asset-preview">
					<img class="ui-state-active ui-corner-all" src="/asset/view/<?= $asset->id?>/160">
				</a>

			</div>

			<div id="sledge-asset-detailview-attributes<?=$asset->id;?>" class="ui-helper-left">
				<table>
					<tr>
						<td>Title</td>
						<td>
							<input type="text" name="title" class="sledge-input" value="<?= $asset->title?>" />
						</td>
					</tr>
					<tr>
						<td>Filename</td>
						<td>
							<input type="text" name="filename" class="sledge-input" value="<?= $asset->filename?>" />
						</td>
					</tr>
					<tr>
						<td style="vertical-align: top">Description</td>
						<td>
							<textarea name="description" class="sledge-textarea"><?= $asset->description?></textarea>
						</td>
					</tr>
					<tr>
						<td>Status</td>
						<td>
							<select name="ref_status_rid">
								<?
									echo "<option value='" . Model_Asset::STATUS_PUBLISHED . "'";
									if ($asset->status === Model_Asset::STATUS_PUBLISHED)
									{
										echo " selected='selected'";
									}
									echo ">Published</option>";

									echo "<option value='" . Model_Asset::STATUS_UNPUBLISHED . "'";
									if ($asset->status === Model_Asset::STATUS_UNPUBLISHED)
									{
										echo " selected='selected'";
									}
									echo ">Unpublished</option>";
								?>									
							</select>
						</td>
					</tr>
					<tr>
						<td>Visible from</td>
						<td>
							<input type="text" name="visible_from" class="sledge-datepicker sledge-input" value="<?= date('d F Y', $asset->visible_from);?>" />
						</td>
					</tr>
				</table>
			</div>

			<div id="sledge-asset-detailview-info<?=$asset->id;?>" class="ui-helper-left">

				<table width="100%">
					<tr>
						<td width="100">Type</td>
						<td><?= $asset->type;?></td>
					</tr>
					<tr>
						<td>Filesize</td>
						<td><?= $asset->filesize ?></td>
					</tr>
					<?
						if ($asset->type == 'image'):
					?>
					<tr>
						<td>Dimensions</td>
						<td><?=$asset->width?> x <?=$asset->height?></td>
					</tr>
					<?
						endif;
					?>
					<tr>
						<td>Uploaded by</td>
						<td><?= $asset->first_version()->person->getName()?></td>
					</tr>
					<tr>
						<td>Upload on</td>
						<td><?= date('d F Y h:i:s', $asset->first_version()->audit_time)?></td>
					</tr>
					<tr>
						<td>Versions</td>
						<td>This asset has been updated <?= $asset->revisions->count_all() ?> times.</td>
					</tr>
				</table>

			</div>

			<div id="sledge-asset-detailview-metadata<?=$asset->id;?>" class="ui-helper-left">
				metadata
			</div>

			<div id="sledge-asset-detailview-folders<?=$asset->id;?>" class="ui-helper-left">
			</div>

			<div id="sledge-asset-detailview-relationships<?=$asset->id;?>" class="ui-helper-left">
				relationships
			</div>

			<br class="ui-helper-clear" />

			<div style="padding: .8em 0 .8em .8em;border-color:#ccc;border-width:1px 0 0 0;" class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
				<button class="sledge-button ui-button-text-icon sledge-tagmanager-asset-save" rel="<?=$asset->id?>">
					<span class="ui-button-icon-primary ui-icon ui-icon-disk"></span>
					Save
				</button>
				<button class="sledge-button ui-button-text-icon sledge-tagmanager-asset-delete" rel="<?=$asset->id?>">
					<span class="ui-button-icon-primary ui-icon ui-icon-circle-close"></span>
					Delete
				</button>
				<button class="sledge-button ui-button-text-icon sledge-tagmanager-asset-download" id="sledge-tagmanager-download-asset">
					<span class="ui-button-icon-primary ui-icon ui-icon-arrowreturn-1-s"></span>
					Download
				</button>
			</div>
		</div>
	
	</form>

</div>
