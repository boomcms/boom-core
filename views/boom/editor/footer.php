	<div id="boom-dialogs">
		<div id="boom-dialog-alerts">
			<p>&nbsp;</p>
		</div>
	</div>

	<div id="boom-loader-dialog-overlay" class="ui-widget-overlay"></div>

	<? $assets = Assets::factory('boom_js_'.(string) $register_page)
		->js('boom.helpers.js')
		->js('jquery.js')
		->js('jquery.ui.js')
		->js('jquery.plugins.js')
		->js('boom.plugins.js')
		->js('boom.config.js')
		->js('boom.core.js')
		->js('boom.page.js'); ?>

	<? if ($register_page):
		$assets
			->js('boom.chunk.js')
			->js('boom.helpers.js')
			->js('boom.browser.js')
			->js('boom.assets.js')
			->js('boom.links.js')
			->js('boom.tags.js');
	endif; ?>

	<?= $assets ?>

	<script type="text/javascript">
		//<![CDATA[
		(function($){

			$.boom.init();
			
			$( 'body' ).page({
				id: <?= $page->id ?>,
				vid: <?= $page->version()->id ?>
			});

			<? if ($register_page): ?>
				$( 'body' ).page( 'register', {
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
