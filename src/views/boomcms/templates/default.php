<!DOCTYPE html>
<html dir="ltr" lang="en-gb" class="boom">
    <head>
        <title><?= $page->getTitle() ?> | <?= Settings::get('site.name') ?></title>
        <meta name="description" content="<?= $page->getDescription() ?>" />
        <meta name="keywords" content="<?= $page->getKeywords() ?>" />

        <?php if ($page->allowsExternalIndexing()): ?>
            <meta name='robots' content='index, follow' />
        <?php else: ?>
            <meta name='robots' content='noindex, nofollow' />
        <?php endif ?>

        <link rel="stylesheet" type="text/css" href="/vendor/boomcms/boom-core/css/default-template.css" />
    </head>

    <body id="default-template">
        <header>
            <h1 id="b-page-title"><?= $page->getTitle() ?></h1>
        </header>

        <main>
            <?= $chunk('text', 'standfirst') ?>
            <?= $chunk('text', 'bodycopy') ?>
        </main>
    </body>
</html>
