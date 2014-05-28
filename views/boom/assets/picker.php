<?= new View('boom/assets/thumbs', array('assets' => $assets)) ?>

<section id="b-assets-picker-sidebar">
	<section id="b-assets-picker-current">
		<h1>Current Asset</h1>
	</section>

	<section id="b-assets-picker-upload">
		<h1>Upload Asset</h1>
		<?= new View('boom/assets/upload') ?>
	</section>

	<section id="b-assets-picker-buttons">
		<?= Boom\UI::button('cancel', 'Close asset picker', array('class' => 'b-button-withtext')) ?>
	</section>
</section>