<?php
/**
* This is the main template for the template manager.
* This template should not be included directly.
*
* Rendered by: Boom_Controller_Cms_Templates::action_index()
*/
?>
	<?= View::factory('boom/header',
		array(
			'title' =>	'Templates',
		))
	?>

	<div id="boom-topbar" class="ui-helper-clearfix ui-tabs ui-widget ui-widget-content ui-corner-all">

		<?= Menu::factory('boom') ?>

		<div class="ui-helper-clearfix ui-tabs-panel ui-widget-content ui-corner-bottom">
			<div id="s-page-actions" class="ui-helper-right">
				<button id="s-templates-save" class="boom-button ui-button-text-icon ui-icon-disk">
					Save all
				</button>
			</div>
		</div>
	</div>

	<div id="boom-dialogs">
		<div id="boom-dialog-alerts">
			<p>&nbsp;</p>
		</div>
	</div>

	<div id="boom-loader-dialog-overlay" class="ui-widget-overlay"></div>
	<div id="s-templates">
		<form>
			<?= Form::hidden('csrf', Security::token()) ?>
			<table id="s-templates-table" class="tablesorter">
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th>Name</th>
						<th>Description</th>
						<th>Filename</th>
						<th>Visible</th>
						<th>Pages</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<? foreach ($templates as $t): ?>
						<tr
						<?
							if ( ! $t->file_exists()):
								echo " class='s-templates-nofile'";
							elseif (in_array($t->pk(), $imported)):
								echo " class='s-templates-new'";
							endif
						?>
						>
							<td><input type='hidden' name='templates[]' value='<?= $t->pk() ?>' /></td>
							<td><input type='text' name='name-<?= $t->id ?>' value="<?= $t->name ?>" /></td>
							<td><input type='text' name='description-<?= $t->id ?>' value="<?= $t->description ?>" /></td>
							<td>
								<select name='filename-<?= $t->id ?>'>
									<? /*
										This is inefficient in that we have to loop through the template array ((n * n) + 1) times (where n is the number of templates.
										It would be better to generate the HTML string for the option list once and reuse it.
										This would prevent us highlighting the current filename though.
										Performance shouldn't become an issue since most sites won't have alot of templates.
										*/
									?>
									<? foreach ($filenames as $filename): ?>
									 	<option value="<?= $filename ?>"
											<? if ($filename == $t->filename): ?>
												selected='selected'
											<? endif; ?>

											><?= $filename ?>
										</option>
									<? endforeach; ?>
								</select>
							</td>
							<td><?= Form::select("visible-$t->id", array( 1 => 'Yes', 0 => 'No'), (int) $t->visible, array('class' => 'boom-templates-input')) ?></td>
							<td>
								<? $page_count = $t->page_count(); ?>
								<a href='/cms/templates/pages/<?= $t->pk() ?>' title='View the title and URL of <?= $page_count, " ", Inflector::plural('page', $page_count) ?> which use this template'><?= $page_count ?>
							</td>
							<td><a class="ui-button-icon-primary ui-icon ui-icon-trash s-templates-delete" title="Delete the &quot;<?= $t->name ?>&quot; template" href="#">&nbsp;</a>
						</tr>
					<? endforeach; ?>
				</tbody>
			</table>
		</form>
	</div>

	<div id="s-templates-preview" style="display: none;">
	  <img src=""/>
	</div>

	<?= HTML::script("media/boom/js/boom.helpers.js") ?>
	<?= HTML::script("media/boom/js/jquery.js") ?>
	<?= HTML::script("media/boom/js/boom.jquery.ui.js") ?>
	<?= HTML::script("media/boom/js/boom.plugins.js") ?>
	<?= HTML::script("media/boom/js/boom.config.js") ?>
	<?= HTML::script("media/boom/js/boom.core.js") ?>
	<?= HTML::script("media/boom/js/boom.helpers.js") ?>
	<?= HTML::script("media/boom/js/boom.templates.js") ?>

	<script type="text/javascript">
		//<![CDATA[
		(function($){
			$.boom.init('templates', {
				person: {
					rid: <?= $person->id?>,
					name: "<?= $person->name?>"
				}
			});

			$.boom.templates.init();
			$('#s-templates table')
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
