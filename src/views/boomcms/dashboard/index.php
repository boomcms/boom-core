<?= view('boomcms::header', ['title' => trans('boomcms::dashboard.heading')]) ?>
<?= $menu() ?>

<div id="b-topbar" class="b-toolbar">
    <?= $menuButton() ?>
</div>

<main id="b-container">
    <div id="b-dashboard">
        <h1><?= trans('boomcms::dashboard.welcome', ['name' => $person->getName()]) ?></h1>

        <div class="cols">
            <div>
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

                <?php if (count($pages)): ?>
                    <section>
                        <h2><?= trans('boomcms::dashboard.recent-pages') ?></h2>

                        <ol class="page-list">
                            <?php foreach ($pages as $p): ?>
                                <li>
                                    <a href="<?= $p->url() ?>">
                                        <h3><?= $p->getTitle() ?></h3>
                                        <time datetime="<?= $p->getVisibleFrom()->format('d M Y H:i') ?>"></time>
                                        <p><?= $p->url() ?></p>
                                        <p><?= Chunk::get('text', 'standfirst', $p)->text() ?></p>
                                    </a>
                                </li>
                            <?php endforeach ?>
                        </ol>
                    </section>
                <?php endif ?>

                <?php if (Gate::allows('managePages', Router::getActiveSite())): ?>
                    <section>
                        <h2><?= trans('boomcms::dashboard.approvals.heading') ?></h2>

                        <?php if (count($approvals)): ?>
                            <ol class='page-list'>
                                <?php foreach ($approvals as $p): ?>
                                    <li>
                                        <a href="<?= $p->url() ?>">
                                            <h3><?= $p->getTitle() ?></h3>
                                            <time datetime="<?= $p->getVisibleFrom()->format('d M Y H:i') ?>"></time>
                                            <p><?= $p->url() ?></p>
                                            <p><?= Chunk::get('text', 'standfirst', $p)->text() ?></p>
                                        </a>
                                    </li>
                                <?php endforeach ?>
                            </ol>
                        <?php else: ?>
                            <p><?= trans('boomcms::dashboard.approvals.none') ?></p>
                        <?php endif ?>
                    </section>
                <?php endif ?>
            </div>

            <div>
                <img class="logo" src="/vendor/boomcms/boom-core/img/logo.png" alt="BoomCMS Logo">

                <?php if (!empty($news)): ?>
                    <section>
                        <h2><?= trans('boomcms::dashboard.news.heading') ?></h2>

                        <ol class="page-list">
                            <?php foreach ($news as $item): ?>
                                <li>
                                    <a href="<?= $item->url ?>?utm_source=dashboard&amp;utm_medium=<?= Request::server('HTTP_HOST') ?>">
                                        <h3><?= $item->title ?></h3>
                                        <time datetime="<?= (new DateTime("@{$item->date}"))->format('d M Y H:i') ?>"></time>
                                        <p><?= $item->standfirst ?></p>
                                    </a>
                                </li>
                            <?php endforeach ?>
                        </ol>
                    </section>
                <?php endif ?>
            </div>
        </div>
    </div>
</main>

<?= view('boomcms::footer') ?>
