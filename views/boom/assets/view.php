<div id="s-assets-view" class="s-items-view">
	<form onsubmit="return false;">
		<?= Form::hidden('csrf', Security::token()) ?>
		<input type="hidden" name="id" id="asset_id" value="<?= $asset->id?>" />

		<div class="sledge-tabs ui-helper-clearfix">

			<ul>
				<li><a href="#s-assets-view-attributes<?=$asset->id;?>"><?=__('Attributes')?></a></li>
				<li><a href="#s-assets-view-info<?=$asset->id;?>"><?=__('Info')?></a></li>
				<li><a href="#s-assets-view-tags<?=$asset->id;?>"><?=__('Tags')?></a></li>
				<? if (count($asset->old_files()) > 0): ?>
					<li><a href="#s-assets-view-files<?=$asset->id;?>"><?=__('Previous Files')?></a></li>
				<? endif; ?>
			</ul>

			<div class="s-assets-preview ui-tabs-panel ui-widget-content ui-helper-left">

				<a href="<?= Route::url('asset', array('action' => 'thumb', 'id' => $asset->id, 'width' => 600, 'height' => 500)) ?>"
					title="<?= $asset->title?>"
					title="Click for larger view"
					class="ui-helper-left sledge-asset-preview">
					<img class="ui-state-active ui-corner-all" src="<?= Route::url('asset', array('action' => 'thumb', 'id' => $asset->id, 'width' => 160, 'height' => 160, 'quality' => 85, 'crop' => 1)) ?>">
				</a>

			</div>

			<div id="s-assets-view-attributes<?=$asset->id;?>" class="ui-helper-left">
				<table>
					<tr>
						<td><label for="title"><?=__('Title')?></label></td>
						<td>
							<input type="text" id="title" name="title" class="sledge-input" value="<?= $asset->title?>" />
						</td>
					</tr>
					<tr>
						<td style="vertical-align: top"><label for="description"><?=__('Description')?></label></td>
						<td>
							<textarea id="description" name="description" class="sledge-textarea"><?= $asset->description?></textarea>
						</td>
					</tr>
					<tr>
						<td><label for="visible_from"><?=__('Visible from')?></label></td>
						<td>
							<input type="text" id="visible_from" name="visible_from" class="sledge-datepicker sledge-input" value="<?= date('d F Y', $asset->visible_from);?>" />
						</td>
					</tr>
				</table>
			</div>

			<div id="s-assets-view-info<?=$asset->id;?>" class="ui-helper-left">

				<table width="100%">
					<? if ($asset->type == Sledge_Asset::BOTR AND ! $asset->encoded): ?>
						<tr>
							<td width="100"><?=__('Video encoding')?></td>
							<td>&nbsp;</td>
						</tr>
					<? endif; ?>
					<tr>
						<td width="100"><?=__('Type')?></td>
						<td><?= ucfirst(Sledge_Asset::get_type($asset->type));?></td>
					</tr>
					<tr>
						<td><?=__('Filesize')?></td>
						<td><?= Text::bytes($asset->filesize) ?></td>
					</tr>
					<? if ($asset->type == Sledge_Asset::BOTR): ?>
						<tr>
							<td><?=__('Duration')?></td>
							<td><?= gmdate("i:s", $asset->duration) ?></td>
						</tr>
					<? endif; ?>
					<? if ($asset->type == Sledge_Asset::IMAGE): ?>
						<tr>
							<td><?=__('Dimensions')?></td>
							<td><?=$asset->width?> x <?=$asset->height?></td>
						</tr>
					<? endif; ?>
					<tr>
						<td><?=__('Uploaded by')?></td>
						<td><?= $asset->uploader->name ?></td>
					</tr>
					<tr>
						<td><?=__('Uploaded on')?></td>
						<td><?= date('d F Y h:i:s', $asset->uploaded_time)?></td>
					</tr>
				</table>

			</div>

			<div id="s-assets-view-tags<?=$asset->id;?>" class="ui-helper-left">
				<ul style="width: 290px;" class="sledge-tree s-tags-tree sledge-tree-noborder">
				<?
					foreach($asset->get_tags(NULL, FALSE) as $tag):
						$name = str_replace("Tags/Assets/Folders/", "", $tag->path);
						echo "<li><a href='#tag/", $tag->id, "'>", $name, "</a></li>";
					endforeach
				?>
				</ul>
				<button id="sledge-button-asset-tags-add" class="sledge-button ui-button-text-icon" data-icon="ui-icon-circle-plus">
					<?=__('Add Tags')?>
				</button>
				<button id="sledge-button-asset-tags-delete" class="sledge-button ui-button-text-icon" data-icon="ui-icon-trash">
					<?=__('Delete Selected Tags')?>
				</button>
			</div>

			<? if (count($asset->old_files()) > 0): ?>
				<div id="s-assets-view-files<?= $asset->id ?>" class="ui-helper-left">
					<p>
						These files were previously assigned to this asset but were replaced.
					</p>
					<ul>
						<? foreach ($asset->old_files() as $version_id => $filename): ?>
							<li>
								<img src="<?= Route::url('asset', array('action' => 'thumb', 'id' => $asset->id, 'width' => 160, 'height' => 160, 'quality' => 85, 'crop' => 1)) ?>">
								<? if ($version_id): ?>
									 ?version=<?= $version_id ?>
								<? endif; ?>
								" />
							</li>
						<? endforeach; ?>
					</ul>
				</div>
			<? endif; ?>

			<br class="ui-helper-clear" />

			<div style="padding: .8em 0 .8em .8em;border-color:#ccc;border-width:1px 0 0 0;" class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
				<button class="sledge-button ui-button-text-icon sledge-tagmanager-asset-save" rel="<?=$asset->id?>" data-icon="ui-icon-disk">
					<?=__('Save')?>
				</button>
				<button class="sledge-button ui-button-text-icon sledge-tagmanager-asset-delete" rel="<?=$asset->id?>" data-icon="ui-icon-circle-close">
					<?=__('Delete')?>
				</button>
				<button class="sledge-button ui-button-text-icon sledge-tagmanager-asset-download" rel="<?=$asset->id?>" data-icon="ui-icon-arrowreturn-1-s">
					<?=__('Download')?>
				</button>
				<button class="sledge-button ui-button-text-icon sledge-tagmanager-asset-replace" rel="<?=$asset->id?>" data-icon="ui-icon-disk">
					<?=__('Replace')?>
				</button>
			</div>
		</div>
	</form>

	<? if ($asset->type == Sledge_Asset::BOTR AND ! $asset->encoded): ?>
		<script language="JavaScript" type="text/javascript">
			setInterval(function(){
				window.location.reload();
			}, 3000);
		</script>
	<? endif; ?>
</div>