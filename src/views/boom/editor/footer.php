	<script type="text/javascript" src="/public/boom/js/cms.js"></script>

	<script type="text/javascript">
		//<![CDATA[
		$(document).ready(function () {
            console.log($.boom);
			$.boom.init();

			$('body').pageEditor({
				page_id : <?= $page->getId() ?>,
				editable : <?= (int) ($editor->isEnabled() && ($auth->loggedIn('edit_page_content', $page) || $page->wasCreatedBy($person))) ?>,
				publishable : <?= (int) $auth->loggedIn('publish_page', $page) ?>
			});
		});
		//]]>
	</script>
</body>
</html>
