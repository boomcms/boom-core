	<?= Boom::include_js()->render() ?>

	<script type="text/javascript">
		//<![CDATA[
		$(document).ready(function() {
			$.boom.init({
				csrf: '<?= Security::token() ?>'
			});

			$('body').pageEditor({
				page_id : <?= $page->getId(); ?>,
				editable : <?= (int) (\Boom\Editor\Editor::instance()->isEnabled() && ($auth->loggedIn('edit_page_content', $page) || $page->wasCreatedBy($person))) ?>,
				publishable : <?= (int) $auth->loggedIn('publish_page', $page) ?>
			});
		});
		//]]>
	</script>
</body>
</html>