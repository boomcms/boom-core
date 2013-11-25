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
				editable : <?= (int) (Editor::instance()->state_is(Editor::EDIT) AND ($auth->logged_in('edit_page_content', $page) OR $page->was_created_by($person))) ?>
			});
		});
		//]]>
	</script>
</body>
</html>