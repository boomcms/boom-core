	<div id="boom-dialogs">
		<div id="boom-dialog-alerts">
			<p>&nbsp;</p>
		</div>
	</div>

	<div id="boom-loader-dialog-overlay" class="ui-widget-overlay"></div>

	<? $assets = Assets::factory('boom_js_'.(string) $register_page)
		->js('boom.helpers.js')
		->js('jquery.js')
		->js('boom.jquery.ui.js')
		->js('boom.plugins.js')
		->js('boom.config.js')
		->js('boom.core.js')
		->js('boom.page.js'); ?>

	<? if ($register_page):
		$assets
			->js('boom.chunk.js')
			->js('boom.helpers.js')
			->js('boom.tagmanager.js')
			->js('boom.items.js')
			->js('boom.assets.js')
			->js('boom.links.js');
	endif; ?>

	<?= $assets ?>

	<script type="text/javascript">
		//<![CDATA[
		(function($){

			$.boom.init('sites', {
				person: {
					rid: <?= $person->id?>,
					name: "<?= $person->name?>"
				}
			});

			$.boom.page.init({
				defaultRid: 1,
				<?
					if (isset( $page )):
						echo "id: $page->id,";
						echo "vid: ", $page->version()->id;
					endif;
				?>
			});

			<? if ($register_page): ?>
				$.boom.page.register({
					rid: <?=$page->id;?>,
					vid: <?=$page->version()->id;?>,
					writable: <?= (int) ($auth->logged_in('edit_page_content', $page) OR $page->was_created_by($person)) ?>,
					editorOptions: {}
				});
			<? endif; ?>
		})(jQuery);
		//]]>
	</script>
</body>
</html>
