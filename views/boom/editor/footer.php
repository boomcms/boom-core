	<div id="boom-dialogs">
		<div id="boom-dialog-alerts">
			<p>&nbsp;</p>
		</div>
	</div>

	<?= Boom::include_js() ?>

	<script type="text/javascript">
		//<![CDATA[
		$(document).ready(function() {
			$.boom.init({
				csrf: '<?= Security::token() ?>'
			});

			$('body').pageEditor({
				page_id : <?= $page->id; ?>,
				editable : <?= (int) (Editor::instance()->state_is(Editor::EDIT) && ($auth->logged_in('edit_page_content', $page) || $page->was_created_by($person))) ?>,
				publishable : <?= (int) $auth->logged_in('publish_page', $page) ?>
			});
		});
		//]]>
	</script>
</body>
</html>