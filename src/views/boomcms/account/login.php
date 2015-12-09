<!DOCTYPE html>
<html dir="ltr" lang="en-gb" class="boom">
	<head>
		<title>BoomCMS | <?= trans('Login') ?></title>
		<meta name="robots" content="noindex, nofollow" />
		<link rel="stylesheet" type="text/css" href="/vendor/boomcms/boom-core/css/cms.css" />
	</head>

	<body id='b-login'>
		<div>
			<div id="logo"></div>

			<form name="login-form" action="/cms/login" method="post">
				<input type="hidden" name="_token" value="<?php echo csrf_token() ?>">

				<fieldset>
					<?php if (isset($login_error)): ?>
						<p class="b-error"><?= $login_error ?></p>
					<?php endif ?>
					<p>
						<label for="email">
							<?= trans('Email address') ?>
						</label>
						<input type="email" placeholder='<?= trans('Email address') ?>' name="email" required id="email" value="<?= $request->input('email') ?>" />
					</p>

					<p>
						<label for="password">
							<?= trans('Password') ?>
						</label>
						<input type="password" placeholder='<?= trans('Password') ?>' name="password" required id="password" />
					</p>

					<p>
						<label class="b-remember-me-label">
							<input type="checkbox" name="remember" class="b-remember-me" value='1' />
							<?= trans('Keep me signed in') ?> (<?= trans('until you log out') ?>)
						</label>
					</p>

					<input type='submit' value='<?= trans('Login') ?>' />
					<a id='b-login-recover-link' href='/cms/recover'>I've forgotten my password</a>
				</fieldset>
			</form>
		</div>
	</body>
</html>
