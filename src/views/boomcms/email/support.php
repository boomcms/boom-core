<?= view('boomcms::email.header') ?>

<div style="text-align: left">
    <h2>Hello,</h2>
    <p>A support request has been submitted via the BoomCMS toolbar. The details are below:</p>

    <h3 style="font-size: 14px; margin: 0"><?= trans('boomcms::support.form.subject') ?></h3>
    <p style="margin: 5px 0 10px 0"><?= $request->input('subject') ?></p>

    <h3 style="font-size: 14px; margin: 0"><?= trans('boomcms::support.form.message') ?></h3>
    <p style="margin: 5px 0 10px 0"><?= nl2br($request->input('message')) ?></p>

    <h3 style="font-size: 14px; margin: 0"><?= trans('boomcms::support.name') ?></h3>
    <p style="margin: 5px 0 10px 0"><?= $person->getName() ?></p>

    <h3 style="font-size: 14px; margin: 0"><?= trans('boomcms::support.email') ?></h3>
    <p style="margin: 5px 0 10px 0"><?= $person->getEmail() ?></p>

    <h3 style="font-size: 14px; margin: 0"><?= trans('boomcms::support.browser') ?></h3>
    <p style="margin: 5px 0 10px 0"><?= $request->input('browser') ?></p>

    <h3 style="font-size: 14px; margin: 0"><?= trans('boomcms::support.width') ?></h3>
    <p style="margin: 5px 0 10px 0"><?= $request->input('viewport-width') ?></p>

    <h3 style="font-size: 14px; margin: 0"><?= trans('boomcms::support.height') ?></h3>
    <p style="margin: 5px 0 10px 0"><?= $request->input('viewport-height') ?></p>

    <h3 style="font-size: 14px; margin: 0"><?= trans('boomcms::support.location') ?></h3>
    <p style="margin: 5px 0 10px 0"><?= $request->input('location') ?></p>
</div>

<?= view('boomcms::email.footer') ?>
