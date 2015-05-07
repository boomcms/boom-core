	<?= View::make('boom::header', ['title' => 'Pages']) ?>
	<?= new \BoomCMS\Core\Menu\Menu($auth)  ?>

	<div id="b-topbar" class="b-toolbar">
		<?= new \BoomCMS\Core\UI\MenuButton() ?>
	</div>

	<div id="b-pages">
		<ul class='boom-tree'>
			<?php foreach ($pages as $p): ?>
                            <li data-page-id="<?= $p->getId() ?>" data-children="<?= (int) $p->getMptt()->has_children() ?>"><a id="page_<?= $p->getId() ?>" href="<?= $p->url() ?>" rel="<?= $p->getId() ?>"><?= $p->getTitle() ?></a></li>
			<?php endforeach ?>
		</ul>
	</div>

	<script type="text/javascript">
		//<![CDATA[
		(function ($) {
			$.boom.init();

			$('body').ui();
			$('.boom-tree').pageManager();
		})(jQuery);
		//]]>
	</script>
</body>
</html>
