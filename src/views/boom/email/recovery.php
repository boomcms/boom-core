<h2>Hello <?= $person->getName() ?></h2>
<p style="text-align:left;">We've received a request to create a new password on the <a href="<?= URL::base($request) ?>" style="color:#404040; text-decoration:underline !important;font-weight:bold;"><?= $site_name ?></a> website.</p>

<p style="text-align:left;">To create a new CMS password please follow the link below<br /><br /><a href="<?= URL::site('/cms/recover', $request)."?token={$token->token}" ?>" style="color:#404040; text-decoration:underline !important;font-weight:bold;"><?= URL::site('/cms/recover', $request)."?token={$token->token}" ?></a>.</p>
