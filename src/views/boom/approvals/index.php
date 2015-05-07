    <?= View::make('boom/header', ['title' => 'Pending Approvals']) ?>
    <?= new \Boom\Menu\Menu  ?>

    <div id="b-topbar" class="b-toolbar">
        <?= new \Boom\UI\MenuButton() ?>
    </div>

    <div id="b-approvals">
        <h1>Pages pending approval</h1>

        <?php if (count($pages)): ?>
            <table id="b-items-view-list" class="b-table">
                <tr>
                    <th>Page title</th>
                    <th>Last edited by</th>
                    <th>Time of last edit</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                <?php foreach ($pages as $page): ?>
                    <tr class="<?= Text::alternate('odd', 'even') ?>" data-page-id="<?= $page->getId() ?>">
                        <td><a href="<?= $page->url() ?>"><?= $page->getTitle() ?></a></td>
                        <td><?= $page->getCurrentVersion()->person->name ?> (<?= $page->getCurrentVersion()->person->email ?>)</td>
                        <td><?= date('d F Y H:i', $page->getCurrentVersion()->edited_time) ?></td>
                        <td><a href="#" class="b-approvals-publish">Publish</a></td>
                        <td><a href="#" class="b-approvals-reject">Revert to published version</a></td>
                        <td><a href="<?= $page->url() ?>">View page</a></td>
                    </tr>
                <?php endforeach ?>
            </table>
        <?php else: ?>
            <p>
                None!
            </p>
        <?php endif ?>
    </div>

    <?= Assets::factory('boom_approvals')->js('boom/approvals.js') ?>

    <script type="text/javascript">
        //<![CDATA[
        (function ($) {
            $.boom.init();
        })(jQuery);
        //]]>
    </script>
</body>
</html>
