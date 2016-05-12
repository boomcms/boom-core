<head>
    <title><?= $title ?></title>
    
    <meta name="robots" content="noindex, nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta name="csrf-token" content="<?= csrf_token() ?>" />

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/vendor/boomcms/boom-core/css/cms.css" />

    <script type="text/javascript">
        paceOptions = {
            ajax: true,
            startOnPageLoad: false,
            restartOnRequestAfter: 100
        }; 
    </script>

    <?= $boomJS ?>
</head>
