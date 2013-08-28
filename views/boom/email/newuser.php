<h2>Hello <?= $person->name ?></h2>
<p style="text-align:left;">The system administrator created an account for you on the <a href="<?= URL::base($request) ?>" style="color:#404040; text-decoration:underline !important;font-weight:bold;"><?= $site_name ?></a> website.<br />Here are your login details:</p>
<p style="text-align:left;"><span style="font-weight:bold;">Email:</span> <?= $person->email ?></p>
<p style="text-align:left;"><span style="font-weight:bold;">Password:</span> <?= $password ?></p>
<p style="text-align:left;">Please go to <a href="<?= URL::site('cms/login', $request) ?>" style="color:#404040; text-decoration:underline !important;font-weight:bold;"><?= URL::site('cms/login', $request) ?></a> to login.</p>