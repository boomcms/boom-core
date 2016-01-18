    <?= view('boomcms::header', ['title' => 'Pending Approvals']) ?>
    <?= $menu() ?>

    <div id="b-topbar" class="b-toolbar">
        <?= $menuButton() ?>
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
                    <?php $editedBy = $page->getCurrentVersion()->getEditedBy() ?>

                    <?php if ($auth->loggedIn('publish', $page)): ?>
                        <tr data-page-id="<?= $page->getId() ?>">
                            <td><a href="<?= $page->url() ?>"><?= $page->getTitle() ?></a></td>
                            <td><?= $editedBy ? $editedBy->getName() : '' ?> (<?= $editedBy ? $editedBy->getEmail() : '' ?>)</td>
                            <td><?= $page->getCurrentVersion()->getEditedTime()->format('d F Y H:i') ?></td>
                            <td><a href="#" class="b-approvals-publish">Publish</a></td>
                            <td><a href="#" class="b-approvals-reject">Revert to published version</a></td>
                            <td><a href="<?= $page->url() ?>">View page</a></td>
                        </tr>
                    <?php endif ?>
                <?php endforeach ?>
            </table>
        <?php else : ?>
            <p>
                None!
            </p>
        <?php endif ?>
    </div>

    <?= $boomJS ?>

    <script type="text/javascript">
        //<![CDATA[
        (function ($) {
            $.boom.init();
        })(jQuery);
        //]]>
    </script>
</body>
</html>
