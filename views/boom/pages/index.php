	<?= View::factory('boom/header')->set('title', 'Pages') ?>

	<div id="boom-topbar" class="ui-helper-clearfix ui-tabs ui-widget ui-widget-content ui-corner-all">
		<?= Menu::factory('boom')->sort('priority') ?>
	</div>

	<div id="b-page-edit">
		<div id="b-items-list">
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

						echo "' id='", $page->id, "' href='", $page->url(), "' rel='", $page->id, "'>";

							echo " ", $page->version()->title, " ", $page->url(), " Template: ", $page->version()->template->name, " ";

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

	<?= Boom::include_js() ?>

	<script type="text/javascript">
		//<![CDATA[
		(function($){
			$.boom.init();

			$('.boom-tree-item').click(function(){
				window.location = $(this).attr('href');

				return false;
			});
		})(jQuery);
		//]]>
	</script>
</body>
</html>