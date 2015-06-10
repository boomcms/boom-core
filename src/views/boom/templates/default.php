<html>
    <head>
        <title><?= $page->getTitle() ?></title>
    </head>
    
    <body>
        <h1 id="b-page-title"><?= $page->getTitle() ?></h1>
        <?= Chunk::view('text', 'standfirst') ?>
        <?= Chunk::view('text', 'bodycopy') ?>
    </body>
</html>