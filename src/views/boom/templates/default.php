<html>
    <head>
        <title><?= $page->getTitle() ?></title>
    </head>
    
    <body>
        <h1 id="b-page-title"><?= $page->getTitle() ?></h1>
        <?= Chunk::factory('text', 'standfirst') ?>
        <?= Chunk::factory('text', 'bodycopy') ?>
    </body>
</html>