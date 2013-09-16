<div id="b-assets-view" class="b-items-view">
	<form onsubmit="return false;" class="b-form">
		<?= Form::hidden('csrf', Security::token()) ?>
		<input type="hidden" name="id" id="asset_id" value="<?= $asset->id?>" />

		<div class="boom-tabs ui-helper-clearfix">
			<ul>
				<li><a href="#b-assets-view-attributes<?=$asset->id;?>"><?=__('Attributes')?></a></li>
				<li><a href="#b-assets-view-info<?=$asset->id;?>"><?=__('Info')?></a></li>
				<li class="b-dialog-hidden"><a href="#b-assets-view-tags<?=$asset->id;?>"><?=__('Tags')?></a></li>
				<? if (count($asset->old_files()) > 0): ?>
					<li class="b-dialog-hidden"><a href="#b-assets-view-files<?=$asset->id;?>"><?=__('Previous Files')?></a></li>
				<? endif; ?>
			</ul>

			<div class="b-assets-preview ui-tabs-panel ui-widget-content ui-helper-left">
				<a href="<?= Route::url('asset', array('action' => 'thumb', 'id' => $asset->id, 'width' => 600, 'height' => 500)) ?>"
					title="Click for larger view"
					class="ui-helper-left boom-asset-preview">
					<img class="ui-state-active ui-corner-all" src="<?= Route::url('asset', array('action' => 'thumb', 'id' => $asset->id, 'width' => 160, 'height' => 160, 'quality' => 85, 'crop' => 1)) ?>">
				</a>
			</div>

			<div id="b-assets-view-attributes<?=$asset->id;?>" class="b-assets-view-attributes ui-helper-left">
				<label>
					<?=__('Title')?>
					<input type="text" id="title" name="title" value="<?= $asset->title ?>" />
				</label>

				<label>
					<?=__('Description')?>
					<textarea id="description" name="description"><?= $asset->description ?></textarea>
				</label>

				<label>
					<?=__('Credits')?>
					<textarea id="credits" name="credits"><?= $asset->credits ?></textarea>
				</label>

				<label>
					<?= __('Visible from') ?>
					<input type="text" id="visible_from" name="visible_from" class="boom-datepicker" value="<?= date('d F Y h:m', $asset->visible_from);?>" />
				</label>

				<? if ($asset->type != Boom_Asset::IMAGE): ?>
					<label for="thumbnail">Thumbnail asset ID
						<input type="text" id="thumbnail" name="thumbnail_asset_id" value="<?= $asset->thumbnail_asset_id ?>" size="4" />
					</label>
				<? endif; ?>
			</div>

			<div id="b-assets-view-info<?=$asset->id;?>" class="ui-helper-left">
				<dl>
					<dt><?=__('Type')?></dt>
					<dd><?= ucfirst(Boom_Asset::type($asset->type));?></dd>

					<dt><?=__('Filesize')?></dt>
					<dd><span id='filesize'><?= Text::bytes($asset->filesize) ?></dd>

					<? if ($asset->type == Boom_Asset::IMAGE): ?>
						<dt><?=__('Dimensions')?></dt>
						<dd><?=$asset->width?> x <?=$asset->height?></dd>
					<? endif; ?>

					<? if ($asset->uploaded_by): ?>
						<dt><?=__('Uploaded by')?></dt>
						<dd><?= $asset->uploader->name ?></dd>
					<? endif; ?>

					<dt><?=__('Uploaded on')?></dt>
					<dd><?= date('d F Y h:i:s', $asset->uploaded_time)?></dd>

					<? if ($asset->type != Boom_Asset::IMAGE): ?>
						<dt><?=__('Downloads')?></dt>
						<dd><?= Num::format($asset->downloads) ?></dd>
					<? endif ?>
				</dl>
			</div>

			<div id="b-assets-view-tags<?=$asset->id;?>" class="b-dialog-hidden ui-helper-left">
				<?= View::factory('boom/tags/list', array('tags' => $tags)) ?>
			</div>

			<? if (count($asset->old_files()) > 0): ?>
				<div id="b-assets-view-files<?= $asset->id ?>" class="b-dialog-hidden ui-helper-left">
					<p>
						These files were previously assigned to this asset but were replaced.
					</p>
					<ul>
						<? foreach ($asset->old_files() as $timestamp => $filename): ?>
							<li>
								<a href="/cms/assets/restore/<?= $asset->id ?>?timestamp=<?= $timestamp ?>">
									<img src="<?= Route::url('asset', array('action' => 'thumb', 'id' => $asset->id, 'width' => 160, 'height' => 160, 'quality' => 85, 'crop' => 1)) ?><? if ($timestamp): ?>?timestamp=<?= $timestamp ?><? endif; ?>" />
								</a>
								<?=date("d F Y H:i", $timestamp);?>
							</li>
						<? endforeach; ?>
					</ul>
				</div>
			<? endif; ?>

			<br class="ui-helper-clear" />

			<div style="padding: .8em 0 .8em .8em;border-color:#ccc;border-width:1px 0 0 0;" class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
				<a href="#tag/0" class="boom-button ui-button-text-icon b-assets-back" rel="<?=$asset->id?>" data-icon="ui-icon-boom-back">
					<?=__('Back')?>
				</a>
				<button class="b-dialog-hidden boom-button ui-button-text-icon b-assets-save" rel="<?=$asset->id?>" data-icon="ui-icon-boom-accept">
					<?=__('Save')?>
				</button>
				<button class="b-dialog-hidden boom-button ui-button-text-icon b-assets-delete" rel="<?=$asset->id?>" data-icon="ui-icon-boom-delete">
					<?=__('Delete')?>
				</button>
				<button class="b-dialog-hidden boom-button ui-button-text-icon b-assets-download" rel="<?=$asset->id?>" data-icon="ui-icon-boom-download">
					<?=__('Download')?>
				</button>
				<button class="b-dialog-hidden boom-button ui-button-text-icon b-assets-replace" rel="<?=$asset->id?>" data-icon="ui-icon-boom-replace">
					<?=__('Replace')?>
				</button>
			</div>
		</div>
	</form>
</div>