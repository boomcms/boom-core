<div id="b-assets-content">
	<div id="b-assets-view-thumbs" class="ui-helper-left">
		<? foreach ($assets as $asset): ?>
			<div style="height: 160px; width: <?= floor(160 * $asset->getAspectRatio()) ?>px" data-aspect-ratio="<?= $asset->getAspectRatio() ?>">
				<div class="thumb" data-asset="<?= $asset->getId() ?>">
					<a href="#asset/<?=$asset->getId()?>">
						<img src="/asset/thumb/<?=$asset->getId()?>/400/0" />

						<section class="b-asset-details">
							<h1><?= $asset->getTitle() ?></h1>

							<p>
								<strong>Type</strong> <?= $asset->getType() ?><br />
								<strong>Description</strong> <?= ($asset->getDescription())? Text::limit_words($asset->getDescription(), 5) : 'None set' ?><br />

								<? if ($asset->getDownloads()): ?>
									<strong>Downloads</strong> <?= $asset->getDownloads() ?><br />
								<? endif ?>

								<? if ($asset->getWidth() && $asset->getHeight()): ?>
									<strong>Dimensions</strong> <?= $asset->getWidth() ?> x <?= $asset->getHeight() ?><br />
								<? endif ?>
							</p>
						</section>
					</a>
				</div>
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
</div>
<div id="b-assets-stats">
	<?= Num::format($total, 0) ?> <?= Inflector::plural('file', $total) ?> / <?= Text::bytes($total_size) ?>
</div>