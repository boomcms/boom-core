<form name="pagesettings-adminsettings" id="sledge-form-pagesettings-adminsettings">
	<div id="s-pagesettings" class="sledge-tabs s-pagesettings">
		<table width="100%">
			<tr>
				<td><?=__('Internal name')?></td>
				<td>
					<?= Form::input('internal_name', $page->internal_name, array('class' => 'sledge-input', 'id' => 'internal_name')); ?>
				</td>
			</tr>
		</table>
	</div>
</form>