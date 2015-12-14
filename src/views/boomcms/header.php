<!DOCTYPE html>
<html lang="en" dir="ltr" class="boom">
    <head>
        <title><?= $title ?></title>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <meta http-equiv="Content-Style-Type" content="text/css" />
        <meta name="csrf-token" content="<?= csrf_token() ?>" />

        <link rel="stylesheet" type="text/css" href="/vendor/boomcms/boom-core/css/cms.css" />
    </head>
    <body>
        <?= view('boomcms::editor.linkPicker') ?>
