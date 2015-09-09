<nav id="b-menu" class="pushy pushy-left">
	<img src="/vendor/boomcms/boom-core/img/logo.png" alt="BoomCMS Logo" />

    <ul>
        <li>
            <a target='_top' href='/' class="fa fa-home"><?= Lang::get('boom::menu.home') ?></a>
        </li>

        <?php foreach (BoomCMS\Support\Menu::items() as $item): ?>
            <li>
                <a target='_top' href='<?= $item['url'] ?>'<?php if (isset($item['icon'])): ?> class="fa fa-<?= $item['icon'] ?>"<?php endif ?>><?= $item['title'] ?></a>
            </li>
        <?php endforeach ?>

        <li>
            <a target='_top' href='/cms/account' class="fa fa-user"><?= Lang::get('boom::menu.account') ?></a>
        </li>

        <li>
            <a target='_top' href='/cms/logout' class="fa fa-sign-out"><?= Lang::get('boom::menu.logout') ?></a>
        </li>
    </ul>
</nav>
