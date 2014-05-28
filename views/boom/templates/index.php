	<?= View::factory('boom/header', array('title' =>	'Templates'))?>
	<?= Menu::factory('boom')->sort('priority') ?>

	<div id="b-topbar" class="b-toolbar">
		<?= \Boom\UI::button('menu', __('Menu'), array('id' => 'b-menu-button', 'class' => 'menu-btn')) ?>
		<?= \Boom\UI::button('accept', __('Save all'), array('id' => 'b-templates-save')) ?>
	</div>

	<div id="b-templates">
		<form id="b-items-view-list">
			<?= Form::hidden('csrf', Security::token()) ?>
			<table id="b-templates-table" class="tablesorter">
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th>Name</th>
						<th>Description</th>
						<th>Filename</th>
						<th>Pages</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<? foreach ($templates as $t): ?>
						<? $class = "" ?>
						<? if ( ! $t->fileExists()): ?>
							<? $class = ' b-templates-nofile' ?>
						<? elseif (in_array($t->getId(), $imported)): ?>
							 <? $class = ' b-templates-new' ?>
						<? endif ?>
						<tr class="<?= Text::alternate('odd', 'even') ?><?= $class ?>">
							<td><input type='hidden' name='templates[]' value='<?= $t->getId() ?>' /></td>
							<td><input type='text' name='name-<?= $t->getId() ?>' value="<?= $t->getName() ?>" /></td>
							<td><input type='text' name='description-<?= $t->getId() ?>' value="<?= $t->getDescription() ?>" /></td>
							<td><input type="text" name="filename-<?= $t->getId() ?>" value="<?= $t->getFilename() ?>" /></td>
							<td>
								<? $page_count = $t->countPages(); ?>
								<a href='/cms/templates/pages/<?= $t->getId() ?>' title='View the title and URL of <?= $page_count, " ", Inflector::plural('page', $page_count) ?> which use this template'><?= $page_count ?>
							</td>
							<td><?= Boom\UI::button('delete', "Delete the &quot;{$t->getName()}&quot; template") ?>
						</tr>
					<? endforeach; ?>
				</tbody>
			</table>
		</form>
	</div>

	<?= Boom::include_js() ?>

	<script type="text/javascript">
		//<![CDATA[
		(function($){
			$.boom.init();

			$.boom.templates.init();
			$('#b-templates table')
				.tablesorter({
					/**
					Return the value of any form input in a table cell, or the text content of the cell.
					*/
					textExtraction: function( node ){
						var text = $( node )
							.find( 'select, input' )
							.val();

						return (typeof text == 'undefined') ? $( node ).text() : text;
					},
					sortList: [[1,0]]
				});
		})(jQuery);
		//]]>
	</script>
</body>
</html>