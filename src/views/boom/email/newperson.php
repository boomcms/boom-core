<?= View::make('boom::email.header') ?>

<h2>Hello <?= $person->getName() ?></h2>
<p style="text-align:left;"><?= $createdBy ?> created an account for you on the <a href="<?= url('/') ?>" style="color:#404040; text-decoration:underline !important;font-weight:bold;"><?= $siteName ?></a> website.<br />Here are your login details:</p>
<p style="text-align:left;"><span style="font-weight:bold;">Email:</span> <?= $person->getEmail() ?></p>
<p style="text-align:left;"><span style="font-weight:bold;">Password:</span> <?= $password ?></p>
<p style="text-align:left;">Please go to <a href="<?= url('/cms/login') ?>" style="color:#404040; text-decoration:underline !important;font-weight:bold;"><?= url('/cms/login') ?></a> to login.</p>

<?= View::make('boom::email.footer') ?>
