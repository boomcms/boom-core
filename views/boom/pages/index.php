	<?= View::factory('boom/header', array('title' => 'Pages'))?>
	<?= new \Boom\Menu\Menu  ?>

	<div id="b-topbar" class="b-toolbar">
		<?= new \Boom\UI\MenuButton() ?>
	</div>

	<div id="b-pages">
		<ul class='boom-tree'>
			<? foreach ($pages as $p): ?>
				<li><a href="<?= $p->url() ?>" rel="<?= $p->getId() ?>"><?= $p->getTitle() ?></a></li>
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
