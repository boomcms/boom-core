<head>
    <title><?= $title ?> | BoomCMS - <?= Settings::get('site.name') ?></title>

    <meta name="robots" content="noindex, nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta name="csrf-token" content="<?= csrf_token() ?>" />

    <link rel="icon" href="data:;base64,iVBORw0KGgo=">
    <link rel="stylesheet" type="text/css" href="/vendor/boomcms/boom-core/css/cms.css" />
</head>

<body>
  <div class="not-found">
    <img class="unhappy" src="/vendor/boomcms/boom-core/img/unhappy.png" alt="Not Found" />
    
    <div class="message">
      <p><strong>Oops!</strong> This is awkward ... You are looking for</p>
      <p>something that doesn't actually exist.</p>
    </div>
    
    <a href="http://www.boomcms.net/" class="boom-link" target="_blank">
      <img src="/vendor/boomcms/boom-core/img/logo.png" alt="BoomCMS Logo" />
    </a>
  </div>
</body>
</html>
