<?= View::make('boomcms::email.header') ?>

<h2>Hello <?= $person->getName() ?></h2>
<p style="text-align:left;">We've received a request to create a new password on the <a href="<?= url() ?>" style="color:#404040; text-decoration:underline !important;font-weight:bold;"><?= Settings::get('site.name') ?></a> website.</p>

<p style="text-align:left;">To create a new CMS password please follow the link below<br /><br /><a href="<?= url('/cms/recover/set-password')."?token={$token}" ?>" style="color:#404040; text-decoration:underline !important;font-weight:bold;"><?= url('/cms/recover/set-password')."?token={$token}" ?></a>.</p>

<?= View::make('boomcms::email.footer') ?>
