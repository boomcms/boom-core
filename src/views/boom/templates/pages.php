	<?= View::factory('boom/header', ['title' =>    'Templates'])?>
	<?= new \Boom\Menu\Menu  ?>

	<div id="b-topbar" class="b-toolbar">
		<?= new \Boom\UI\MenuButton() ?>
	</div>

    <div>
        <table>
            <tr>
                <th>Page title</th>
                <th>URL</th>
            </tr>
            <?php foreach ($pages as $p): ?>
                    <tr>
                        <td><?= $p->getTitle() ?></td>
                        <td><a href='<?= $p->url() ?>'><?= $p->url()->location ?></a></td>
                    </tr>
            <?php endforeach ?>
        </table>
    </div>

	<?= Boom::include_js() ?>

	<script type="text/javascript">
		//<![CDATA[
		(function ($) {
			$.boom.init();
		})(jQuery);
		//]]>
	</script>
</body>
</html>
