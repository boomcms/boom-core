<div class='b-page-settings'>
    <ul class="b-page-settings-menu">
        <?php if (Gate::allows('editTemplate', $page)): ?>
            <li>
                <a href="#" data-b-page-setting="template">
                    <span class="fa fa-file-text-o"></span>
                    <?= trans('boomcms::settings.menu.template') ?>
                </a>
            </li>
        <?php endif ?>

        <?php if (Gate::allows('editNavBasic', $page)): ?>
            <li>
                <a href="#" data-b-page-setting="navigation">
                    <span class="fa fa-sitemap"></span>
                    <?= trans('boomcms::settings.menu.navigation') ?>
                </a>
            </li>
        <?php endif ?>

        <?php if (Gate::allows('editUrls', $page)): ?>
            <li>
                <a href="#" data-b-page-setting="urls">
                    <span class="fa fa-link"></span>
                    <?= trans('boomcms::settings.menu.urls') ?>
                </a>
            </li>
        <?php endif ?>

        <?php if (Gate::allows('editSearchBasic', $page)): ?>
            <li>
                <a href="#" data-b-page-setting="search">
                    <span class="fa fa-search"></span>
                    <?= trans('boomcms::settings.menu.search') ?>
                </a>
            </li>
        <?php endif ?>

        <li>
            <a href="#" data-b-page-setting="tags">
                <span class="fa fa-tag"></span>
                <?= trans('boomcms::settings.menu.tags') ?>
            </a>
        </li>

        <li>
            <a href="#" data-b-page-setting="relations">
                <span class="fa fa-puzzle-piece"></span>
                <?= trans('boomcms::settings.menu.relations') ?>
            </a>
        </li>

        <?php if (Gate::allows('editChildrenBasic', $page)): ?>
            <li>
                <a href="#" data-b-page-setting="children">
                    <span class="fa fa-child"></span>
                    <?= trans('boomcms::settings.menu.children') ?>
                </a>
            </li>
        <?php endif ?>

        <?php if (Gate::allows('editAdmin', $page)): ?>
            <li>
                <a href="#" data-b-page-setting="admin">
                    <span class="fa fa-lock"></span>
                    <?= trans('boomcms::settings.menu.admin') ?>
                </a>
            </li>
        <?php endif ?>

        <?php if (Gate::allows('editFeature', $page)): ?>
            <li>
                <a href="#" data-b-page-setting="feature">
                    <span class="fa fa-image"></span>
                    <?= trans('boomcms::settings.menu.feature') ?>
                </a>
            </li>
        <?php endif ?>

        <div class="group">
            <?php if (Gate::allows('publish', $page)): ?>
                <li>
                    <a href="#" data-b-page-setting="visibility">
                        <span class="fa fa-eye"></span>
                        <?= trans('boomcms::settings.menu.visibility') ?>
                    </a>
                </li>
            <?php endif ?>

            <li>
                <a href="#" data-b-page-setting="drafts">
                    <span class="fa fa-pencil"></span>
                    <?= trans('boomcms::settings.menu.drafts') ?>
                </a>
            </li>
        </div>

        <?php if (Gate::allows('delete', $page)): ?>
            <li>
                <a href="#" data-b-page-setting="delete">
                    <span class="fa fa-trash-o"></span>
                    <?= trans('boomcms::settings.menu.delete') ?>
                </a>
            </li>
        <?php endif ?>

        <li class="b-page-settings-close">
            <a href="#">
                <span class="fa fa-close"></span>
                <?= trans('boomcms::settings.menu.close') ?>
            </a>
        </li>
    </ul>

    <div class="b-page-settings-content"></div>
</div>
