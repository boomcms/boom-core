<div id="b-assets-view">
	<form onsubmit="return false;" class="b-form">
		<?= Form::hidden('csrf', Security::token()) ?>
		<?= Form::hidden('id', $asset->id, array('id' => 'asset_id')) ?>

		<div class="b-assets-preview">
			<a href="<?= Route::url('asset', array('action' => 'thumb', 'id' => $asset->id, 'width' => 600, 'height' => 500)) ?>"
				title="Click for larger view"
				class="boom-asset-preview">
				<img src="<?= Route::url('asset', array('action' => 'thumb', 'id' => $asset->id, 'width' => 0, 'height' => 300, 'quality' => 85, 'crop' => 0)) ?>">
			</a>

			<div class="ui-dialog-buttonpane">
				<button class="boom-button ui-button-text-icon b-assets-back" rel="<?=$asset->id?>" data-icon="ui-icon-boom-back">
					<?=__('Back')?>
				</button>
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

		<div class="boom-tabs">
			<ul>
				<li><a href="#b-assets-view-attributes<?=$asset->id;?>"><?=__('Attributes')?></a></li>
				<li><a href="#b-assets-view-info<?=$asset->id;?>"><?=__('Info')?></a></li>
				<li class="b-dialog-hidden"><a href="#b-assets-view-tags<?=$asset->id;?>"><?=__('Tags')?></a></li>
				<? if (count($asset->old_files()) > 0): ?>
					<li class="b-dialog-hidden"><a href="#b-assets-view-files<?=$asset->id;?>"><?=__('Previous Files')?></a></li>
				<? endif; ?>
			</ul>

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
						<dd><?= Num::format($asset->downloads, 0) ?></dd>
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
		</div>
	</form>
</div>