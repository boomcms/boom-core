<?= view('boomcms::header', ['title' => trans('boomcms::dashboard.heading')]) ?>
<?= $menu() ?>

<div id="b-topbar" class="b-toolbar">
    <?= $menuButton() ?>
    <div class="welcome-name"><?= trans('boomcms::dashboard.welcome', ['name' => $person->getName()]) ?></div>

    <ul class="b-toolbar-list">
        <li><button><span class="fa fa-phone"></span> 020 7690 5431</button></li>
        <li><button onclick="window.open('https://www.boomcms.net/boom-support', '_blank')"><span class="fa fa-book"></span> User Guidelines</button></li>
        <li><button onclick="window.open('https://www.boomcms.net', '_blank')"><span class="fa fa-star"></span> About Boom</button></li>
        <li><img class="corner-logo" src="/vendor/boomcms/boom-core/img/logo.png" alt="BoomCMS Logo"></li>
    </ul>

  
</div>

<main id="b-container">
    <div id="b-dashboard">
        
        <div class="cols">
            <div class="col-7">
                <div class="buttons">
                    <a href="/">
                        <span class="fa fa-globe"></span>
                        <?= trans('boomcms::dashboard.view-site') ?>
                    </a>

                    <?php if (Gate::allows('manageAssets', Router::getActiveSite())): ?>
                        <a href="/boomcms/asset-manager">
                            <span class="fa fa-picture-o"></span>
                            <?= trans('boomcms::dashboard.asset-manager') ?>
                        </a>
                    <?php endif ?>

                    <?php if (Gate::allows('uploadAssets', Router::getActiveSite())): ?>
                        <a href="/boomcms/asset-manager/upload">
                            <span class="fa fa-upload"></span>
                            <?= trans('boomcms::dashboard.asset-upload') ?>
                        </a>
                    <?php endif ?>

                    <?php if (Gate::allows('managePages', Router::getActiveSite())): ?>
                        <a href="/boomcms/page-manager">
                            <span class="fa fa-sitemap"></span>
                            <?= trans('boomcms::dashboard.page-manager') ?>
                        </a>
                    <?php endif ?>

                    <?php if (Gate::allows('manageAccount', Router::getActiveSite())): ?>
                        <a href="/boomcms/account">
                            <span class="fa fa-user"></span>
                            <?= trans('boomcms::dashboard.manage-account') ?>
                        </a>
                    <?php endif ?>
                </div>
            </div>

            
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
                                        <time datetime="<?= $p->getVisibleFrom()->format('d M Y H:i') ?>"></time>
                                        <p><?= $p->url() ?></p>
                                        <p><?= Chunk::get('text', 'standfirst', $p)->text() ?></p>
                                    </a>
                                    <!-- <img src="/asset/<?php // $p->getFeatureImageId() ?>"/> -->
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
