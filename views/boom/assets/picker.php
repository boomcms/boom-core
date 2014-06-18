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

	<section id="b-assets-picker-buttons">
		<?= Boom\UI::button('cancel', 'Close asset picker', array('class' => 'b-button-withtext', 'id' => 'b-asset-picker-close')) ?>
	</section>

	<section class="pagination">
		<a href="#" class="first" data-action="first">&laquo;</a>
		<a href="#" class="previous" data-action="previous">&lsaquo;</a>
		<input type="text" readonly="readonly" data-max-page="<?= $pages ?>" />
		<a href="#" class="next" data-action="next">&rsaquo;</a>
		<a href="#" class="last" data-action="last">&raquo;</a>
	</section>
</section>