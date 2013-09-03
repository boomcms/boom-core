<div class="ui-widget-content">
	<div class="ui-helper-right" style="padding: .4em .6em 0 0;">
		<?
			if (isset($pagination)):
				echo "<div class='boom-pagination ui-helper-left'>", $pagination, "</div>";
			endif;
		?>

		<select id="boom-tagmanager-sortby-select" class="ui-helper-left" style="width: 98px">
			<option value="last_modified-desc" <? if ($sortby == 'last_modified-desc') echo "selected='selected'"; ?>>Most recent</option>
			<option value="last_modified-asc" <? if ($sortby == 'last_modified-asc') echo "selected='selected'"; ?>>Oldest</option>
			<option value="title-asc" <? if ($sortby == 'title-asc') echo "selected='selected'"; ?>>Title A-Z</option>
			<option value="title-desc" <? if ($sortby == 'title-desc') echo "selected='selected'"; ?>>Title Z-A</option>
			<option value="filesize-asc" <? if ($sortby == 'filesize-asc') echo "selected='selected'"; ?>>Size (smallest)</option>
			<option value="filesize-desc" <? if ($sortby == 'filesize-desc') echo "selected='selected'"; ?>>Size (largest)</option>
		</select>
	</div>

	<div id="b-items-view-thumbs" class="b-items-thumbs ui-helper-left">
		<? foreach ($assets as $asset): ?>
			<div style="height: 160px; width: <?= floor(160 * $asset->get_aspect_ratio()) ?>px" data-aspect-ratio="<?= $asset->get_aspect_ratio() ?>">
				<div class="thumb">
					<input type="checkbox" class="b-items-select-checkbox" id="asset-thumb-<?=$asset->id?>" />

					<a href="#asset/<?=$asset->id?>">
						<img src="/asset/thumb/<?=$asset->id?>/400/0" />

						<section class="b-asset-details">
							<h1><?= $asset->title ?></h1>

							<p>
								<strong>Type</strong> <?= ucfirst($asset->type()) ?><br />
								<strong>Last edited</strong> <?= date('M j Y', $asset->last_modified) ?><br />
								<strong>Filesize</strong> <?= Text::bytes($asset->filesize) ?><br />
								<strong>Uploaded on</strong> <?= date('M j Y', $asset->uploaded_time) ?><br />

								<? if ($asset->uploaded_by): ?>
									<strong>Uploaded by</strong> <?= $asset->uploaded_by ?><br />
								<? endif ?>

								<? if ($asset->downloads): ?>
									<strong>Downloads</strong> <?= $asset->downloads ?><br />
								<? endif ?>

								<? if ($asset->width AND $asset->height): ?>
									<strong>Dimensions</strong> <?= $asset->width ?> x <?= $asset->height ?><br />
								<? endif ?>
							</p>
						</section>
					</a>
				</div>
			</div>
		<? endforeach; ?>
	</div>
	<div style="padding: .5em 0 .5em .5em;border-color:#ccc;border-width:1px 0 0 0;" class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
		<div class="ui-helper-right" style="margin: .5em .5em 0 0">
			<?=__('Total files')?>: <?= Num::format($total, 0) ?> | <?=__('Total size')?>: <?= Text::bytes($total_size) ?>
		</div>
	</div>
</div>