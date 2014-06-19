<?= new View('boom/assets/thumbs', array('assets' => $assets)) ?>

<section id="b-assets-picker-sidebar">
	<section id="b-assets-picker-current">
		<h1>Current Asset</h1>
		<img src="" />
	</section>

	<section id="b-assets-picker-upload">
		<h1>Upload Asset</h1>
		<?= new View('boom/assets/upload') ?>
	</section>

	<section id="b-assets-picker-filter" class="ui-front">
		<h1>Filter Assets</h1>

		<?= \Boom\UI::button('accept', 'All assets', array('id' => 'b-assets-picker-all', 'class' => 'b-button-textonly')) ?>

		<input type='text' id="b-assets-filter-title" placeholder="Search by asset name" value="Search by asset name" />

		<?= Form::select('types', array_merge(array('0' => 'Filter by type'), ORM::factory('Asset')->types()), null, array('id' => 'b-assets-types')) ?>

		<div id='b-tags-search'>
			<span class="ui-icon ui-icon-boom-tag"></span>
			<input type='text' class="b-filter-input" placeholder="Type a tag name" value="Type a tag name" />
			<ul class="b-tags-list">
			</ul>
		</div>
	</section>

	<section class="pagination">
		<a href="#" class="first" data-action="first">&laquo;</a>
		<a href="#" class="previous" data-action="previous">&lsaquo;</a>
		<input type="text" readonly="readonly" data-max-page="<?= $pages ?>" />
		<a href="#" class="next" data-action="next">&rsaquo;</a>
		<a href="#" class="last" data-action="last">&raquo;</a>
	</section>

	<section id="b-assets-picker-buttons">
		<?= Boom\UI::button('cancel', 'Close asset picker', array('class' => 'b-button-withtext', 'id' => 'b-asset-picker-close')) ?>
	</section>
</section>