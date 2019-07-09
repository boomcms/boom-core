<?= view('boomcms::email.header') ?>

<div style="text-align: left">
    <h2>Hello <?= $person->getName() ?>,</h2>
    <p><?= trans('boomcms::support.respond') ?></p>
</div>

<?= view('boomcms::email.footer') ?>
