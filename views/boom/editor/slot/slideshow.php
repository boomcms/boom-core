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
				<? $link = $slide->get_link() ?>
				<li>
					<label>
						<input type="radio" value="<?= $slide->id ?>" name="slide" data-asset="<?= $slide->asset_id ?>" data-title="<?= $slide->title ?>" data-url="<?= $link->url() ?>" data-page="<?= $link->is_internal()? $link->get_page()->id : 0 ?>" data-caption="<?= $slide->caption ?>" />
						<img src="<?= Route::url('asset', array('id' => $slide->asset_id)) ?>" />
					</label>
				</li>
			<? endforeach ?>
		</ol>
	</section>

	<section id="b-slideshow-editor-current">
		<h1>Current slide</h1>
		<p class='default'>
			No slide selected.
		</p>
		<p class='default'>
			Click on a slide to the left or add a new slide to edit it here.
		</p>
		<form>
			<p>Click on the slide image to change the image</p>
			<p>You may set a title, caption, or link for the slide. However, whether or not these will be used in the slideshow will depend on the template in use.</p>
			<a href="#"><img src="" /></a>
			<label>
				<p>Title</p>
				<input type="text" name="title" />
			</label>
			
			<label>
				<p>Caption</p>
				<input type="text" name="caption" />
			</label>

			<label>
				<p>Link</p>
				<input type="text" name="url" />
			</label>

			<?= BoomUI::button('delete', 'Delete this slide', array('id' => 'b-slideshow-editor-current-delete', 'class' => 'b-button-withtext')) ?>
		</form>
	</section>
</div>

<div id="b-slideshow-editor-buttons">
	<?= BoomUI::button('delete', __('Delete slideshow'), array('id' => 'b-slideshow-editor-delete', 'class' => 'b-button-textonly')) ?>
	<?= BoomUI::button('add', __('Add slide'), array('id' => 'b-slideshow-editor-add', 'class' => 'b-button-withtext')) ?>
</div>