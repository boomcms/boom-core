<!DOCTYPE html>
<html dir="ltr" lang="en-gb" class="boom">
	<head>
		<title>BoomCMS | <?= Lang::get('Password reset') ?></title>
		<meta name="robots" content="noindex, nofollow" />
		<link rel="stylesheet" type="text/css" href="/vendor/boomcms/boom-core/css/cms.css" />
	</head>
	<body id='b-login'>
		<div>
			<div id="logo"></div>

			<form name="login-form" action="/cms/recover<?php if (isset($token)): ?>/set-password?token=<?= $token ?><?php endif ?>" method="post">
				<input type="hidden" name="_token" value="<?php echo csrf_token() ?>">

				<fieldset>
					<?php if (isset($error)): ?>
						<p class="b-error"><?= $error ?></p>
					<?php endif ?>

					<?php if (isset($token)) : ?>
						<p>
							<label for="email">
								<?= Lang::get('Email address') ?>
							</label>
							<input type="email" placeholder='<?= Lang::get('Email address') ?>' name="email" required class="b-input" id="email" />
						</p>

						<p>
							<label for="password1">
								<?= Lang::get('Enter a new password') ?>
							</label>
							<input type="password" placeholder='<?= Lang::get('Enter a new password') ?>' name="password1" required class="b-input" id="password1" />
						</p>

						<p>
							<label for="password2">
								<?= Lang::get('Re-enter your new password') ?>
							</label>
							<input type="password" placeholder='<?= Lang::get('Re-enter your new password') ?>' name="password2" required class="b-input" id="password2" />
						</p>

						<input type='submit' value='<?= Lang::get('Update my password') ?>' />
					<?php else: ?>
						<p>
							Please enter your email address.
						</p>
						<p>
							An email will be sent to you with a link create a new password.
						</p>
						<p>
							<label for="email">
								<?= Lang::get('Email address') ?>
							</label>
							<input type="email" placeholder='<?= Lang::get('Email address') ?>' name="email" required class="b-input" id="email" />
						</p>

						<input type='submit' value='<?= Lang::get('Send me a reset link') ?>' />
					<?php endif ?>
				</fieldset>
			</form>
		</div>
	</body>
</html>
