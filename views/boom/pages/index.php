	<?= View::factory('boom/header')->set('title', 'Pages') ?>

	<div id="b-topbar" class='b-toolbar'>
		<?= Menu::factory('boom')->sort('priority') ?>
	</div>

	<div id="b-pages">
		<ul class='boom-tree'>
			<? foreach ($pages as $p): ?>
				<li><a href="<?= $p->url() ?>" rel="<?= $p->id ?>"><?= $p->getTitle() ?></a></li>
			<? endforeach ?>
		</ul>
	</div>

	<?= Boom::include_js() ?>

	<script type="text/javascript">
		//<![CDATA[
		(function($){
			$.boom.init({
				csrf: '<?= Security::token() ?>'
			});

			$('body').ui();
			$('.boom-tree').pageManager();
		})(jQuery);
		//]]>
	</script>
</body>
</html>
