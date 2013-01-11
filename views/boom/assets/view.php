<div id="b-assets-view" class="b-items-view">
	<form onsubmit="return false;">
		<?= Form::hidden('csrf', Security::token()) ?>
		<input type="hidden" name="id" id="asset_id" value="<?= $asset->id?>" />

		<div class="boom-tabs ui-helper-clearfix">

			<ul>
				<li><a href="#b-assets-view-attributes<?=$asset->id;?>"><?=__('Attributes')?></a></li>
				<li><a href="#b-assets-view-info<?=$asset->id;?>"><?=__('Info')?></a></li>
				<li><a href="#b-assets-view-tags<?=$asset->id;?>"><?=__('Tags')?></a></li>
				<? if (count($asset->old_files()) > 0): ?>
					<li><a href="#b-assets-view-files<?=$asset->id;?>"><?=__('Previous Files')?></a></li>
				<? endif; ?>
			</ul>

			<div class="b-assets-preview ui-tabs-panel ui-widget-content ui-helper-left">

				<a href="<?= Route::url('asset', array('action' => 'thumb', 'id' => $asset->id, 'width' => 600, 'height' => 500)) ?>"
					title="<?= $asset->title?>"
					title="Click for larger view"
					class="ui-helper-left boom-asset-preview">
					<img class="ui-state-active ui-corner-all" src="<?= Route::url('asset', array('action' => 'thumb', 'id' => $asset->id, 'width' => 160, 'height' => 160, 'quality' => 85, 'crop' => 1)) ?>">
				</a>

			</div>

			<div id="b-assets-view-attributes<?=$asset->id;?>" class="ui-helper-left">
				<label for="title"><?=__('Title')?></label>
				<input type="text" id="title" name="title" class="boom-input" value="<?= $asset->title?>" />
				<label for="description"><?=__('Description')?></label></td>
				<textarea id="description" name="description" class="boom-textarea"><?= $asset->description?></textarea>
				<input type="text" id="visible_from" name="visible_from" class="boom-datepicker boom-input" value="<?= date('d F Y', $asset->visible_from);?>" />
			</div>

			<div id="b-assets-view-info<?=$asset->id;?>" class="ui-helper-left">

					<? if ($asset->type == Boom_Asset::BOTR AND ! $asset->encoded): ?>
					<?=__('Video encoding')?>
					<? endif; ?>
					<?=__('Type')?>
					<?= ucfirst(Boom_Asset::type($asset->type));?>
					
					<?=__('Filesize')?>
					<?= Text::bytes($asset->filesize) ?>
					
					<? if ($asset->type == Boom_Asset::BOTR): ?>
						
						<?=__('Duration')?>
						<?= gmdate("i:s", $asset->duration) ?>
						
					<? endif; ?>
					<? if ($asset->type == Boom_Asset::IMAGE): ?>
						
						<?=__('Dimensions')?>
						<?=$asset->width?> x <?=$asset->height?>
						
					<? endif; ?>
					
					<?=__('Uploaded by')?>
					<?= $asset->uploader->name ?>
					
					
					<?=__('Uploaded on')?>
					<?= date('d F Y h:i:s', $asset->uploaded_time)?>

			</div>

			<div id="b-assets-view-tags<?=$asset->id;?>" class="ui-helper-left">
				<ul style="width: 290px;" class="boom-tree b-tags-tree boom-tree-noborder">
				<?
					foreach($asset->get_tags(NULL, FALSE) as $tag):
						$name = str_replace("Tags/Assets/Folders/", "", $tag->path);
						echo "<li><a href='#tag/", $tag->id, "'>", $name, "</a></li>";
					endforeach
				?>
				</ul>
				<button id="boom-button-asset-tags-add" class="boom-button ui-button-text-icon" data-icon="ui-icon-boom-add">
					<?=__('Add Tags')?>
				</button>
				<button id="boom-button-asset-tags-delete" class="boom-button ui-button-text-icon" data-icon="ui-icon-boom-delete">
					<?=__('Delete Selected Tags')?>
				</button>
			</div>

			<? if (count($asset->old_files()) > 0): ?>
				<div id="b-assets-view-files<?= $asset->id ?>" class="ui-helper-left">
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
				<button class="boom-button ui-button-text-icon boom-tagmanager-asset-save" rel="<?=$asset->id?>" data-icon="ui-icon-boom-save">
					<?=__('Save')?>
				</button>
				<button class="boom-button ui-button-text-icon boom-tagmanager-asset-delete" rel="<?=$asset->id?>" data-icon="ui-icon-boom-delete">
					<?=__('Delete')?>
				</button>
				<button class="boom-button ui-button-text-icon boom-tagmanager-asset-download" rel="<?=$asset->id?>" data-icon="ui-icon-boom-download">
					<?=__('Download')?>
				</button>
				<button class="boom-button ui-button-text-icon boom-tagmanager-asset-replace" rel="<?=$asset->id?>" data-icon="ui-icon-boom-upload">
					<?=__('Replace')?>
				</button>
			</div>
		</div>
	</form>

	<? if ($asset->type == Boom_Asset::BOTR AND ! $asset->encoded): ?>
		<script language="JavaScript" type="text/javascript">
			setInterval(function(){
				window.location.reload();
			}, 3000);
		</script>
	<? endif; ?>
</div>