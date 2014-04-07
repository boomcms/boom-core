<div id='b-page-feature' class="b-pagesettings">
	<section>
		<p id="b-featureimage-none">This page currently has no associated feature image.</p>
		<div>
			<? if ( ! $feature_image_id): ?>
				<img id='b-featureimage-img' style="display: none" />
			<? else: ?>
				<img id='b-featureimage-img' src='<?= Route::url('asset', array('id' => $feature_image_id, 'width' => 250, 'height' => 80)) ?>' />
			<? endif; ?>
		</div>
	</section>
	<section>
		<h1>Images used in page</h1>
		<p>The images which are used in this page are shown below. Click on an image to make it the feature image for the page.</p>

		<? if (count($images_in_page)): ?>
			<ul>
				<? foreach ($images_in_page as $image): ?>
					<li>
						<a href='#'><img src='<?= Route::url('asset', array('id' => $image->id, 'width' => 210, 'height' => 100, 'crop' => 1)) ?>' alt='<?= $image->title ?>'/></a>
					</li>
				<? endforeach ?>
			</ul>
		<? else: ?>
			<p>This page doesn't contain any images.</p>
		<? endif ?>
	</section>
</div>