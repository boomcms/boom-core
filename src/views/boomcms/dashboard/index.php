<?= view('boomcms::header', ['title' => trans('boomcms::dashboard.heading')]) ?>
<?= $menu() ?>

<div id="b-topbar" class="b-toolbar">
    <?= $menuButton() ?>
    <div class="b-welcome-name"><?= trans('boomcms::dashboard.welcome', ['name' => $person->getName()]) ?></div>
    <ul class="b-toolbar-list">
        <li><button><span class="fa fa-phone"></span> <span class="button-text">020 7690 5431</span></button></li>
        <li><button onclick="window.open('https://www.boomcms.net/boom-support', '_blank')"><span class="fa fa-book"></span> <span class="button-text">User Guidelines</span></button></li>
        <li><button onclick="window.open('https://www.boomcms.net', '_blank')"><span class="fa fa-star"></span> <span class="button-text">About Boom</span></button></li>
        <li><img class="b-corner-logo" src="/vendor/boomcms/boom-core/img/logo-white.png" alt="BoomCMS Logo"></li>
    </ul>
</div>

<div class="b-dashboard-button-list">
        <ul>
            <li><a href="/"><span class="fa fa-globe"></span><?= trans('boomcms::dashboard.view-site') ?></a></li>

            <?php if (Gate::allows('manageAssets', Router::getActiveSite())): ?>
                <li><a href="/boomcms/asset-manager"><span class="fa fa-picture-o"></span><?= trans('boomcms::dashboard.asset-manager') ?></a></li>
            <?php endif ?>

            <?php if (Gate::allows('uploadAssets', Router::getActiveSite())): ?>
                <li><a href="/boomcms/asset-manager/upload"><span class="fa fa-upload"></span><?= trans('boomcms::dashboard.asset-upload') ?></a></li>
            <?php endif ?>

            <?php if (Gate::allows('managePages', Router::getActiveSite())): ?>
                <li><a href="/boomcms/page-manager"><span class="fa fa-sitemap"></span><?= trans('boomcms::dashboard.page-manager') ?></a></li>
            <?php endif ?>

            <?php if (Gate::allows('manageAccount', Router::getActiveSite())): ?>
                <li><a href="/boomcms/account"><span class="fa fa-user"></span><?= trans('boomcms::dashboard.manage-account') ?></a></li>
            <?php endif ?>

        </ul>
    </div>


<main id="b-container">
    <div id="b-dashboard">

   


        <div class="cols">


        




            <div>

            <ul class="b-dashboard-pages-control">
        <li>Pages</li>
        <li><button id="btn-recently-added" class="active"><?= trans('boomcms::dashboard.recent-pages') ?></button></li>
        <li><button id="btn-pending-approval" class=""><?= trans('boomcms::dashboard.approvals.heading') ?></button></li>
    </ul>


                <?php if (count($pages)): ?>
                    <section id="recently-added-pages" class="pages-section">
                        <ol class="page-list">
                            <?php foreach ($pages as $p): ?>
                                <li>
                                <a href="<?= $p->url() ?>">
                                <div class="page-info">
                                    <div class="page-image">
                                    <?php if($p->getFeatureImageId() != 0) { ?>
                                        <img src="/asset/<?= $p->getFeatureImageId() ?>"/> 
                                                <?php } else { ?>
                                                    <img src="/vendor/boomcms/boom-core/img/placeholder.png"/> 
                                                    <?php } ?>
                                    </div>
                                    <div class="page-text">
                                        <h3><?= $p->getTitle() ?></h3>
                                        <time datetime="<?= $p->getVisibleFrom()->format('d M Y H:i') ?>"></time>
                                        <div class="page-url"><?= $p->url() ?></div>
                                        <div class="page-standfirst"><?= Chunk::get('text', 'standfirst', $p)->text() ?></div>


                                        
                                    </div>

                                    <div class="page-status">
                                      
                                            <?php if($p->getCurrentVersion()->isPublished() == 1) { ?>
                                                <div class="published">Published</div>
                                                <?php } else { ?>
                                                    <div class="draft">Draft</div>
                                                    <?php } ?>
                                      

                                        <div class="page-visible">
                                            <?php if($p->isVisible() == 1) { ?>
                                                <span class="fa fa-eye"></span>
                                                <?php } else { ?>
                                                    <span class="fa fa-eye-slash"></span>
                                                    <?php } ?>
                                        </div>
                                    </div>
                                    
                                </div>

                                 
                                        
                                        
                                    </a>
                                </li>
                            <?php endforeach ?>
                        </ol>
                    </section>
                <?php endif ?>

                <?php if (Gate::allows('managePages', Router::getActiveSite())): ?>
                    <section id="pending-approval-pages" class="pages-section hidden">
                        <?php if (count($approvals)): ?>
                            <ol class='page-list'>
                                <?php foreach ($approvals as $p): ?>
                                    <li>
                                        <a href="<?= $p->url() ?>">
                                            <h3><?= $p->getTitle() ?></h3>
                                            <time datetime="<?= $p->getVisibleFrom()->format('d M Y H:i') ?>"></time>
                                            <p><?= $p->url() ?></p>
                                            <p><?= Chunk::get('text', 'standfirst', $p)->text() ?></p>
                                            <p><?= 'visible => '.$p->isVisible() ?></p>
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
            <h2 class="section-title"><?= trans('boomcms::dashboard.news.heading') ?></h2>
                <?php if (!empty($news)): ?>
                    <section>
                        

                        <ol class="page-list">
                            <?php foreach ($news as $item): ?>
                                <li>
                                    <a href="<?= $item->url ?>?utm_source=dashboard&amp;utm_medium=<?= Request::server('HTTP_HOST') ?>">
                                    <div class="page-info">
                                    <div class="page-text">
                                    <h3><?= $item->title ?></h3>
                                        <time datetime="<?= (new DateTime("@{$item->date}"))->format('d M Y H:i') ?>"></time>
                                        <p><?= $item->standfirst ?></p>
                                    </div>   
                                    </div>
                                     
                                   
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
