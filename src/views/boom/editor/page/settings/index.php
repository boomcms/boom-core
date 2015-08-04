<div class='b-page-settings'>
    <ul class="b-page-settings-menu">
        <?php if ($auth->loggedIn('edit_page_navigation_basic', $page)): ?>
            <li>
                <a href="#" class="fa fa-sitemap" data-b-page-setting="navigation">
                    <?= Lang::get('boom::settings.menu.navigation') ?>
                </a>
            </li>
        <?php endif ?>

        <?php if ($auth->loggedIn('edit_page_urls', $page)): ?>
            <li>
                <a href="#" class="fa fa-link" data-b-page-setting="urls">
                    <?= Lang::get('boom::settings.menu.urls') ?>
                </a>
            </li>
        <?php endif ?>

        <?php if ($auth->loggedIn('edit_page_search_basic', $page)): ?>
            <li>
                <a href="#" class="fa fa-search" data-b-page-setting="search">
                    <?= Lang::get('boom::settings.menu.search') ?>
                </a>
            </li>
        <?php endif ?>

        <li>
            <a href="#" class="fa fa-tag" data-b-page-setting="tags">
                <?= Lang::get('boom::settings.menu.tags') ?>
            </a>
        </li>

        <?php if ($auth->loggedIn('edit_page_children_basic', $page)): ?>
            <li>
                <a href="#" class="fa fa-child" data-b-page-setting="children">
                    <?= Lang::get('boom::settings.menu.children') ?>
                </a>
            </li>
        <?php endif ?>

        <?php if ($auth->loggedIn('edit_page_admin', $page)): ?>
            <li>
                <a href="#" class="fa fa-lock" data-b-page-setting="admin">
                    <?= Lang::get('boom::settings.menu.admin') ?>
                </a>
            </li>
        <?php endif ?>

        <?php if ($auth->loggedIn('edit_feature_image', $page)): ?>
            <li>
                <a href="#" class="fa fa-image" data-b-page-setting="feature">
                    <?= Lang::get('boom::settings.menu.feature') ?>
                </a>
            </li>
        <?php endif ?>
            
		<?php if ($auth->loggedIn('edit_page', $page)): ?>
            <li>
                <a href="#" class="fa fa-eye" data-b-page-setting="visibility">
                    <?= Lang::get('boom::settings.menu.visibility') ?>
                </a>
            </li>
		<?php endif ?>
            
        <?php if ($auth->loggedIn('edit_page_template', $page)): ?>
            <li>
                <a href="#" class="fa fa-file-text-o" data-b-page-setting="template">
                    <?= Lang::get('boom::settings.menu.template') ?>
                </a>
            </li>
        <?php endif ?>
            
        <li>
            <a href="#" class="fa fa-pencil" data-b-page-setting="drafts">
                <?= Lang::get('boom::settings.menu.drafts') ?>
            </a>
        </li>
    </ul>

    <div class="b-page-settings-content"></div>
</div>