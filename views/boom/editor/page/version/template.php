<form id="b-form-pageversion-template" name="pageversion-template">

	<div class="b-template">
		<table width="100%">
			<tr>
				<td width="180">Template:</td>
				<td>
					<?= Form::select('template_id', $templates, $template_id) ?>
				</td>
			</tr>
		</table>
	</div>
</form>