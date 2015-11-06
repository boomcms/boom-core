	<?= View::make('boomcms::header', ['title' => 'Pages']) ?>
	<?= $menu() ?>

	<div id="b-topbar" class="b-toolbar">
		<?= $menuButton() ?>
	</div>

	<div id="b-pages">
		<ul class='boom-tree'></ul>
	</div>

	<?= $boomJS ?>
	<script type="text/javascript">
		//<![CDATA[
		window.onload = function () {
			$.boom.init();

			$('body').ui();
			$('.boom-tree').pageManager();
		};
		//]]>
	</script>
</body>
</html>
