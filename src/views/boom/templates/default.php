<html>
    <head>
        <title><?= $page->getTitle() ?></title>
    </head>
    
    <body>
        <h1 id="b-page-title"><?= $page->getTitle() ?></h1>
        <?= $chunks['text']['standfirst'] ?>
        <?= $chunks['text']['bodycopy'] ?>
    </body>
</html>