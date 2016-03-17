<nav id="b-menu" class="pushy pushy-left">
	<img src="/vendor/boomcms/boom-core/img/logo.png" alt="BoomCMS Logo" />

    <ul>
        <li>
            <a target='_top' href='/'><span class="fa fa-home"></span><?= trans('boomcms::menu.home') ?></a>
        </li>

        <?php foreach (BoomCMS\Support\Menu::items() as $item): ?>
            <li>
                <a target='_top' href='<?= $item['url'] ?>'><?php if (isset($item['icon'])): ?><span class="fa fa-<?= $item['icon'] ?>"></span><?php endif ?><?= $item['title'] ?></a>
            </li>
        <?php endforeach ?>

        <li>
            <a target='_top' href='/boomcms/account'><span class="fa fa-user"></span><?= trans('boomcms::menu.account') ?></a>
        </li>

        <li>
            <a target='_top' href='/boomcms/logout'><span class="fa fa-sign-out"></span><?= trans('boomcms::menu.logout') ?></a>
        </li>
    </ul>

    <p class="version"><a href="http://www.boomcms.net/" target="_blank">BoomCMS</a><br />v<?= BoomCMS::getVersion() ?></p>
</nav>
