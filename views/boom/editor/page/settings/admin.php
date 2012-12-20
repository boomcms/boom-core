<form name="pagesettings-adminsettings" id="boom-form-pagesettings-adminsettings">
	<div id="s-pagesettings" class="boom-tabs s-pagesettings">
		<table width="100%">
			<tr>
				<td><?=__('Internal name')?></td>
				<td>
					<?= Form::input('internal_name', $page->internal_name, array('class' => 'boom-input', 'id' => 'internal_name')); ?>
				</td>
			</tr>
		</table>
	</div>
</form>