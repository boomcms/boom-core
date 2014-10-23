<!DOCTYPE html>
<html dir="ltr" lang="en-gb">
	<head>
		<title>BoomCMS | <?= __('Login') ?></title>
		<meta name="robots" content="noindex, nofollow" />
		<?= Boom::include_css() ?>
	</head>
	<body id='b-login'>
		<div>
			<form name="login-form" action="/cms/login" method="post">
				<?= Form::hidden('csrf', Security::token()) ?>
				<fieldset>
					<? if (isset($login_error)): ?>
						<p class="b-error"><?= $login_error ?></p>
					<? endif ?>
					<p>
						<label for="email">
							<?= __('Email address') ?>
						</label>
						<input type="email" placeholder='<?= __('Email address') ?>' name="email" required class="b-input" id="email" value="<?= $request->query('email') ?>" />
					</p>
					<p>
						<label for="password">
							<?= __('Password') ?>
						</label>
						<input type="password" placeholder='<?= __('Password') ?>' name="password" required class="b-input" id="password" />
					</p>
					<p>
						<label class="b-remember-me-label">
							<input type="checkbox" name="remember" class="b-remember-me" value='1' />
							<?= __('Keep me signed in') ?> (<?= __('until you log out') ?>)
						</label>
					</p>

					<input type='submit' value='<?= __('Login') ?>' />
					<a id='b-login-recover-link' href='/cms/recover'>I've forgotten my password</a>
				</fieldset>
			</form>
		</div>
	</body>
</html>