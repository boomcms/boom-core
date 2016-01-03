<div class='b-page-settings'>
    <ul class="b-page-settings-menu">
        <?php if ($auth->check('editTemplate', $page)): ?>
            <li>
                <a href="#" class="fa fa-file-text-o" data-b-page-setting="template">
                    <?= trans('boomcms::settings.menu.template') ?>
                </a>
            </li>
        <?php endif ?>

        <?php if ($auth->check('editNavBasic', $page)): ?>
            <li>
                <a href="#" class="fa fa-sitemap" data-b-page-setting="navigation">
                    <?= trans('boomcms::settings.menu.navigation') ?>
                </a>
            </li>
        <?php endif ?>

        <?php if ($auth->check('editUrls', $page)): ?>
            <li>
                <a href="#" class="fa fa-link" data-b-page-setting="urls">
                    <?= trans('boomcms::settings.menu.urls') ?>
                </a>
            </li>
        <?php endif ?>

        <?php if ($auth->check('editSearchBasic', $page)): ?>
            <li>
                <a href="#" class="fa fa-search" data-b-page-setting="search">
                    <?= trans('boomcms::settings.menu.search') ?>
                </a>
            </li>
        <?php endif ?>

        <li>
            <a href="#" class="fa fa-tag" data-b-page-setting="tags">
                <?= trans('boomcms::settings.menu.tags') ?>
            </a>
        </li>

        <li>
            <a href="#" class="fa fa-puzzle-piece" data-b-page-setting="relations">
                <?= trans('boomcms::settings.menu.relations') ?>
            </a>
        </li>

        <?php if ($auth->check('editChildrenBasic', $page)): ?>
            <li>
                <a href="#" class="fa fa-child" data-b-page-setting="children">
                    <?= trans('boomcms::settings.menu.children') ?>
                </a>
            </li>
        <?php endif ?>

        <?php if ($auth->check('editAdmin', $page)): ?>
            <li>
                <a href="#" class="fa fa-lock" data-b-page-setting="admin">
                    <?= trans('boomcms::settings.menu.admin') ?>
                </a>
            </li>
        <?php endif ?>

        <?php if ($auth->check('editFeatureImage', $page)): ?>
            <li>
                <a href="#" class="fa fa-image" data-b-page-setting="feature">
                    <?= trans('boomcms::settings.menu.feature') ?>
                </a>
            </li>
        <?php endif ?>

        <div class="group">
            <?php if ($auth->check('edit', $page)): ?>
                <li>
                    <a href="#" class="fa fa-eye" data-b-page-setting="visibility">
                        <?= trans('boomcms::settings.menu.visibility') ?>
                    </a>
                </li>
            <?php endif ?>

            <li>
                <a href="#" class="fa fa-pencil" data-b-page-setting="drafts">
                    <?= trans('boomcms::settings.menu.drafts') ?>
                </a>
            </li>
        </div>

        <?php if ($auth->check('delete', $page)): ?>
            <li>
                <a href="#" class="fa fa-trash-o" data-b-page-setting="delete">
                    <?= trans('boomcms::settings.menu.delete') ?>
                </a>
            </li>
        <?php endif ?>

        <li class="b-page-settings-close">
            <a href="#" class="fa fa-close">
                <?= trans('boomcms::settings.menu.close') ?>
            </a>
        </li>
    </ul>

    <div class="b-page-settings-content"></div>
</div>