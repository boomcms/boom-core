<!DOCTYPE html>
<html dir="ltr" lang="en-gb" class="boom">
	<head>
		<title>BoomCMS | <?= Lang::get('boomcms::auth.reset.title') ?></title>
		<meta name="robots" content="noindex, nofollow" />
		<link rel="stylesheet" type="text/css" href="/vendor/boomcms/boom-core/css/cms.css" />
	</head>

	<body id='b-login'>
		<div>
			<div id="logo"></div>

			<form name="login-form" action="/cms/recover<?php if (isset($token)): ?>/set-password?token=<?= $token ?><?php endif ?>" method="post">
				<input type="hidden" name="_token" value="<?php echo csrf_token() ?>">

				<fieldset>
                    <?php if (isset($token)): ?>
                        <legend>
                            <?= Lang::get('boomcms::auth.reset.intro') ?>
                        </legend>
                    <?php endif ?>

					<?php if (isset($error)): ?>
						<p class="b-error"><?= $error ?></p>
					<?php endif ?>

					<?php if (isset($token)) : ?>
						<p>
							<label for="email">
								<?= Lang::get('boomcms::auth.reset.email') ?>
							</label>
							<input type="email" placeholder='<?= Lang::get('boomcms::auth.reset.email') ?>' name="email" required id="email" />
						</p>

						<p>
							<label for="password1">
								<?= Lang::get('boomcms::auth.reset.password1') ?>
							</label>
							<input type="password" placeholder='<?= Lang::get('boomcms::auth.reset.password1') ?>' name="password1" required id="password1" />
						</p>

						<p>
							<label for="password2">
								<?= Lang::get('boomcms::auth.reset.password2') ?>
							</label>
							<input type="password" placeholder='<?= Lang::get('boomcms::auth.reset.password2') ?>' name="password2" required id="password2" />
						</p>

						<input type='submit' value='<?= Lang::get('boomcms::auth.reset.update') ?>' />
					<?php else: ?>
						<p>
							<?= Lang::get('boomcms::auth.reset.enter-email') ?>
						</p>
                
						<p>
							<?= Lang::get('boomcms::auth.reset.what-will-happen') ?>
						</p>

						<p>
							<label for="email">
								<?= Lang::get('boomcms::auth.reset.email') ?>
							</label>
							<input type="email" placeholder='<?= Lang::get('boomcms::auth.reset.email') ?>' name="email" required id="email" />
						</p>

						<input type='submit' value='<?= Lang::get('boomcms::auth.reset.send-link') ?>' />
					<?php endif ?>
				</fieldset>
			</form>
		</div>
	</body>
</html>
