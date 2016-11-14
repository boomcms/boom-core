<?= view('boomcms::header', ['title' => trans('boomcms::dashboard.heading')]) ?>
<?= $menu() ?>

<div id="b-topbar" class="b-toolbar">
    <?= $menuButton() ?>
</div>

<main id="b-container">
    <div id="b-dashboard">
        <h1><?= trans('boomcms::dashboard.welcome', ['name' => $person->getName()]) ?></h1>

        <div class="buttons">
            <a href="/">
                <span class="fa fa-globe"></span>
                <?= trans('boomcms::dashboard.view-site') ?>
            </a>

            <a href="/boomcms/account">
                <span class="fa fa-user"></span>
                <?= trans('boomcms::dashboard.manage-account') ?>
            </a>
        </div>

        <div>
            <?php if (count($pages)): ?>
                <section>
                    <h2><?= trans('boomcms::dashboard.recent-pages') ?></h2>

                    <ol class="recent-pages">
                        <?php foreach ($pages as $p): ?>
                            <li>
                                <a href="<?= $p->url() ?>">
                                    <h3><?= $p->getTitle() ?></h3>
                                    <p><?= $p->url() ?></p>
                                    <p><?= Chunk::get('text', 'standfirst', $p)->text() ?></p>

                                    <time datetime="<?= $p->getVisibleFrom()->format('d M Y H:i') ?>"></time>
                                </a>
                            </li>
                        <?php endforeach ?>
                    </ol>
                </section>
            <?php endif ?>
        </div>
    </div>
</main>

<?= view('boomcms::footer') ?>
