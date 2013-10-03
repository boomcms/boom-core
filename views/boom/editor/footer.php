	<div id="boom-loader"></div>
	<div id="boom-dialogs">
		<div id="boom-dialog-alerts">
			<p>&nbsp;</p>
		</div>
	</div>
	<div id="boom-loader-dialog-overlay" class="ui-widget-overlay"></div>

	<?= Boom::include_js() ?>

	<script type="text/javascript">
		//<![CDATA[
		(function($){

			$.boom.init({
				csrf: '<?= Security::token() ?>',
			});

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
