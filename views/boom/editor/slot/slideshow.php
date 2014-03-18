<div class='container'>
	<section>
		<h1>All Slides</h1>
		<p>
			All of the slideshow's slides are shown below.
		</p>
		<ul>
			<li>Drag slides to re-order them.</li>
			<li>Click on an image to edit or delete the slide</li>
			<li>Click on the + button below to add a new slide</li>
		</ul>

		<ol id="b-slideshow-editor-slides">
			<? foreach ($slides as $slide): ?>
				<li>
					<label>
						<input type="radio" value="<?= $slide->id ?>" name="slide" data-title="<?= $slide->title ?>" data-url="<?= $slide->url ?>" data-caption="<?= $slide->caption ?>" />
						<img src="<?= Route::url('asset', array('id' => $slide->asset_id)) ?>" />
					</label>
				</li>
			<? endforeach ?>
		</ol>
	</section>

	<section>
		<h1>Current slide</h1>
		<p class='default'>
			No slide selected.
		</p>
		<p class='default'>
			Click on a slide to the left or add a new slide to edit it here.
		</p>
	</section>
</div>

<div id="b-slideshow-editor-buttons">
	<?= BoomUI::button('delete', __('Delete slideshow'), array('id' => 'b-slideshow-editor-delete')) ?>
	<?= BoomUI::button('add', __('Add slide'), array('id' => 'b-slideshow-editor-add')) ?>
</div>