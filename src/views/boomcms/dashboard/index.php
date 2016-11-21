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

        <div class="cols">
            <div>
                <?php if (count($pages)): ?>
                    <section>
                        <h2><?= trans('boomcms::dashboard.recent-pages') ?></h2>

                        <ol class="page-list">
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

                <?php if (Gate::allows('managePages', Router::getActiveSite())): ?>
                    <section>
                        <h2><?= trans('boomcms::dashboard.approvals.heading') ?></h2>

                        <?php if (count($approvals)): ?>
                            <ol class='page-list'>
                                <li>
                                    <a href="<?= $p->url() ?>">
                                        <h3><?= $p->getTitle() ?></h3>
                                        <p><?= $p->url() ?></p>
                                        <p><?= Chunk::get('text', 'standfirst', $p)->text() ?></p>

                                        <time datetime="<?= $p->getVisibleFrom()->format('d M Y H:i') ?>"></time>
                                    </a>
                                </li>
                            </ol>
                        <?php else: ?>
                            <p><?= trans('boomcms::dashboard.approvals.none') ?></p>
                        <?php endif ?>
                    </section>
                <?php endif ?>
            </div>

            <div>
                <?php if (!empty($news)): ?>
                    <section>
                        <h2><?= trans('boomcms::dashboard.news.heading') ?></h2>
                    </section>
                <?php endif ?>
            </div>
        </div>
    </div>
</main>

<?= view('boomcms::footer') ?>
