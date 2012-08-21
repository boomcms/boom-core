<div id="library-search">
	<form action="<?= $url ?>" method="post">
		<div class="clearfix">
			<h2>Search the library</h2>
		</div>
		<label for="library-categories">To show all documents that relate to a category choose from the dropdown list.</label>
		<select id="library-categories" name="tag">
			<option selected="selected">Choose a category</option>
			<?
				if ( ! empty($kids)):
					foreach ($kids as $kid): ?>
						<option value="<?=$kid['id'] ?>"<?if ($kid['id'] == $tag->pk()): ?> selected="selected"<? endif; ?>>
							<?= $kid['name'] ?>
						</option>
					<? endforeach;
				endif;
			?>
		</select>
		<input type="submit" name="submit" value="Go" />
		<p>or</p>
		<a href="<?= $url ?>">
			<img src="/img/view_all.png" class="view" alt="View all">
		</a>
		<p class="hide"><a href="#">Hide options</a></p>
	</form>
</div>
<div id="main-content" class="library-results span-6 prepend-2">
	<div class="library-options clearfix">
		<p><?=$total?> assets found</p>
	</div>
	
	<?= @$pagination ?>

	<div class="library-results rel-toplevel-<?= $parent_tag->pk() ?>">
		<div id="chunk-gallery" class="clearfix">
			<? if ($total === 0): ?>
				<p>
					There are no assets currently assigned to this tag.
				</p>
			<? endif; ?>

			<? foreach ($assets as $i => $asset): ?>
				<? if ($i %3 == 0): ?>
					<div class="row clearfix">
				<? endif; ?>

				<div class="item<?if (($i+1) %3 == 0): ?> last<? endif; ?>">
					<dl>
						<dt class="image">
							<img src="/asset/thumb/<?= $asset->id ?>/100/100/85/1" alt="<?= $asset->title ?>" />
						</dt>
						<dd class="description">
							<p><?= $asset->title ?> <a onclick="_gaq.push(['_trackEvent', 'Downloads', '<?= $asset->get_type() ?>', '<?=$asset->title?>']);" href="<?= Asset::PATH ?><?=$asset->id?>"><?=$asset->get_type()?> <?= Text::bytes($asset->filesize) ?></a></p>
							<p class="download">
								<a onclick="_gaq.push(['_trackEvent', 'Downloads', '<?=$asset->get_type()?>', '<?=$asset->title?>']);" href="/asset/view/<?=$asset->id?>">
									<img src="/img/download.png" alt="Download <?=$asset->title?>">
								</a>
							</p>		
						</dd>
					</dl>
				</div>
				<? if (($i+1) %3 == 0 || ($i+1) == $total): ?>
					</div>
				<? endif; ?>
			<? endforeach; ?>
		</div>
	</div>

	<?= @$pagination ?>
</div>