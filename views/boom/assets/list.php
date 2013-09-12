<div id="b-assets-content">
	<div id="b-assets-view-thumbs" class="ui-helper-left">
		<? foreach ($assets as $asset): ?>
			<div style="height: 160px; width: <?= floor(160 * $asset->get_aspect_ratio()) ?>px" data-aspect-ratio="<?= $asset->get_aspect_ratio() ?>">
				<a href="#asset/<?=$asset->id?>">
					<label class="thumb">
						<input type="checkbox" class="b-items-select-checkbox" id="asset-thumb-<?=$asset->id?>" />
							<img src="/asset/thumb/<?=$asset->id?>/400/0" />

							<section class="b-asset-details">
								<h1><?= $asset->title ?></h1>

								<p>
									<strong>Type</strong> <?= ucfirst($asset->type()) ?><br />
									<strong>Description</strong> <?= ($asset->description)? Text::limit_words($asset->description, 5) : 'None set' ?><br />

									<? if ($asset->downloads): ?>
										<strong>Downloads</strong> <?= $asset->downloads ?><br />
									<? endif ?>

									<? if ($asset->width AND $asset->height): ?>
										<strong>Dimensions</strong> <?= $asset->width ?> x <?= $asset->height ?><br />
									<? endif ?>
								</p>
							</section>
					</label>
				</a>
			</div>
		<? endforeach; ?>
	</div>
</div>
<div id="b-assets-pagination">
	<?
		if (isset($pagination)):
			echo "<div class='boom-pagination ui-helper-left'>", $pagination, "</div>";
		endif;
	?>

	<?= Form::select('', array(
		'last_modified-desc' => 'Most recent',
		'last_modified-asc' => 'Oldest',
		'title-asc' => 'Title A - Z',
		'title-desc' => 'Title Z - A',
		'filesize-asc' => 'Size (smallest)',
		'filesize-desc' => 'Size (largest)',
		'downloads-desc' => 'Most downloaded'
		), $sortby, array('id' => 'b-assets-sortby'))
	?>
</div>
<div id="b-assets-stats">
	<?= Num::format($total, 0) ?> <?= Inflector::plural('file', $total) ?> / <?= Text::bytes($total_size) ?>
</div>