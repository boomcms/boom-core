	<?= View::factory('boom/header')->set('title', 'Pages') ?>

	<div id="boom-topbar" class="ui-helper-clearfix ui-tabs ui-widget ui-widget-content ui-corner-all">
		<?= Menu::factory('boom') ?>
	</div>

	<div id="boom-dialogs">
		<div id="boom-dialog-alerts">
			<p>&nbsp;</p>
		</div>
	</div>

	<div id="boom-loader-dialog-overlay" class="ui-widget-overlay"></div>
	<div id="s-page-edit">
		<div id="s-items-list">
			<div id="nav" class="boom-tree block">
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
						echo "<li id='p", $node['id'], "'><a class='boom-tree-item' style='";

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

	<div id="boom-template-preview" style="display: none;">
	  <img id="boom-template-preview-image" src=""/>
	</div>

	<?= HTML::script("media/boom/js/boom.helpers.js") ?>
	<?= HTML::script("media/boom/js/jquery.js") ?>
	<?= HTML::script("media/boom/js/boom.jquery.ui.js") ?>
	<?= HTML::script("media/boom/js/boom.plugins.js") ?>
	<?= HTML::script("media/boom/js/boom.config.js") ?>
	<?= HTML::script("media/boom/js/boom.core.js") ?>
	<?= HTML::script("media/boom/js/boom.helpers.js") ?>

	<script type="text/javascript">
		//<![CDATA[
		(function($){
			$.boom.init('templates', {
				person: {
					rid: <?= $person->id?>,
					name: "<?= $person->name?>"
				}
			});

			$('#s-pages-csv').click(function(){
				window.location = '/cms/data/pages';
			});

			$('.boom-tree-item').click(function(){
				window.location = $(this).attr('href');

				return false;
			});
		})(jQuery);
		//]]>
	</script>
</body>
</html>
