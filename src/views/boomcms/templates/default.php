<html>
    <head>
        <title><?= $page->getTitle() ?></title>
    </head>

    <body>
        <h1 id="b-page-title"><?= $page->getTitle() ?></h1>
        <?= Chunk::edit('text', 'standfirst')->render() ?>
        <?= Chunk::edit('text', 'bodycopy')->render() ?>
    </body>
</html>
