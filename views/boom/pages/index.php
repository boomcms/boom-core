	<?= View::factory('boom/header')->set('title', 'Pages') ?>

	<div id="b-topbar">
		<?= Menu::factory('boom')->sort('priority') ?>
	</div>

	<div id="b-pages">
		<ul class='boom-tree'>
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

					echo " ", $page->version()->title, " <span class='url'>(", $page->url(), ")</span> <span class='template'>Template: ", $page->version()->template->name, "</span> ";

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

	<?= Boom::include_js() ?>

	<script type="text/javascript">
		//<![CDATA[
		(function($){
			$.boom.init();

			$('body').ui();

			$('.boom-tree-item').click(function(){
				window.location = $(this).attr('href');

				return false;
			});
		})(jQuery);
		//]]>
	</script>
</body>
</html>
