<?= View::make('boom::email.header') ?>

<h2>Hello <?= $person->getName() ?></h2>
<p style="text-align:left;">The password for your BoomCMS account on the <a href="<?= url('/') ?>" style="color:#404040; text-decoration:underline !important;font-weight:bold;"><?= Settings::get('site.name') ?></a> website was recently changed.</p>
<p style="text-align:left;">If you made this changed then that's great, there's nothing to worry about</p>
<p style="text-align:left;">If you didn't make this change then you should contact your website administrator at <a href="mailto:<?= Settings::get('site.admin.email') ?>"><?= Settings::get('site.admin.email') ?></a></p>

<?= View::make('boom::email.footer') ?>
