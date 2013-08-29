<div style="margin-bottom: .6em">
	<div class="ui-widget">
		<div class="ui-state-highlight ui-corner-all">
			<p style="margin: .5em;">
				<span style="float: left; margin-right: 0.3em; margin-top:-.2em" class="ui-icon ui-icon-info"></span>
				Select a date / time and format below.
			</p>
		</div>
	</div>
	<br />

	<label>
		Format
		<?= Form::select('format', $formats, $format, array('id' => 'format')) ?>
	</label>

	<label>
		Date / time

		<input id="timestamp" name="timestamp" class="boom-input boom-datepicker" value="<?= date("d F Y h:m", $timestamp) ?>" />
	</label>
</div>