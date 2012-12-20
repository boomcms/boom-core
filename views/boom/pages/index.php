	<?= View::factory('sledge/header')->set('title', 'Pages') ?>

	<div id="sledge-topbar" class="ui-helper-clearfix ui-tabs ui-widget ui-widget-content ui-corner-all">
		<?= Menu::factory('sledge') ?>
	</div>

	<div id="sledge-dialogs">
		<div id="sledge-dialog-alerts">
			<p>&nbsp;</p>
		</div>
	</div>

	<div id="sledge-loader-dialog-overlay" class="ui-widget-overlay"></div>
	<div id="s-page-edit">
		<div id="s-items-list">
			<div id="nav" class="sledge-tree block">
				<ul>
				<?
					$level = 1;
					$count = sizeof($pages);

					for ($i = 0; $i < $count; $i++)
					{
						$node = $pages[$i]['mptt'];
						$page = $pages[$i]['page'];

						// Going down?
						if ($i < ($count - 1) && $pages[ $i + 1 ]['mptt']['lvl'] > $node['lvl'])
						{
							$level = $node['lvl'];
						}

						// Going up?
						if ($i > 0 && $node['lvl'] < $pages[ $i - 1 ]['mptt']['lvl'])
						{
							echo str_repeat("</li></ul></li>", $pages[ $i - 1 ]['mptt']['lvl'] - $node['lvl']);
							$level = $node['lvl'];
						}

						// Show the page.
						echo "<li id='p", $node['id'], "'><a class='sledge-tree-item' style='";

						if ( ! $page->is_visible())
						{
							echo " background-color: grey;";
						}
						if (! $page->visible_in_nav)
						{
							echo " color: #ff0000;";
						}

						echo "' id='", $page->id, "' href='/", $page->primary_link(), "' rel='", $page->id, "'>";

							echo "<table style='border: none'><tr><td>", $page->version()->title, "</td><td>", $page->link(), "</td><td>Template: ", $page->version()->template->name, "</td></tr></table>";

						echo "</a>\n";

						// Start a sub-list if this page has children. Otherwise close the list item.
						if ($i < ($count - 1) && $pages[ $i + 1 ]['mptt']['parent_id'] == $node['id'])
						{
							echo "<ul class='hidden ui-helper-hidden'>";
						}
						else
						{
							echo "</li>";
						}
					}
				?>

			</div>
		</div>
	</div>

	<div id="sledge-template-preview" style="display: none;">
	  <img id="sledge-template-preview-image" src=""/>
	</div>

	<?= HTML::script("media/sledge/js/sledge.helpers.js") ?>
	<?= HTML::script("media/sledge/js/jquery.js") ?>
	<?= HTML::script("media/sledge/js/sledge.jquery.ui.js") ?>
	<?= HTML::script("media/sledge/js/sledge.plugins.js") ?>
	<?= HTML::script("media/sledge/js/sledge.config.js") ?>
	<?= HTML::script("media/sledge/js/sledge.core.js") ?>
	<?= HTML::script("media/sledge/js/sledge.helpers.js") ?>

	<script type="text/javascript">
		//<![CDATA[
		(function($){
			$.sledge.init('templates', {
				person: {
					rid: <?= $person->id?>,
					name: "<?= $person->name?>"
				}
			});

			$('#s-pages-csv').click(function(){
				window.location = '/cms/data/pages';
			});

			$('.sledge-tree-item').click(function(){
				window.location = $(this).attr('href');

				return false;
			});
		})(jQuery);
		//]]>
	</script>
</body>
</html>
