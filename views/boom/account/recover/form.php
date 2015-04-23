<!DOCTYPE html>
<html dir="ltr" lang="en-gb">
	<head>
		<title>BoomCMS | <?= __('Password reset') ?></title>
		<meta name="robots" content="noindex, nofollow" />
		<link rel="stylesheet" type="text/css" href="/media/boom/css/cms.css" />
	</head>
	<body id='b-login'>
		<div>
			<form name="login-form" action="/cms/recover<? if (isset($token)): ?>?token=<?= $token->token ?><? endif ?>" method="post">
				<?= Form::hidden('csrf', Security::token()) ?>
				<fieldset>
					<? if (isset($error)): ?>
						<p class="b-error"><?= $error ?></p>
					<? endif ?>

					<? if (isset($token)) : ?>
						<?= Form::hidden('csrf', Security::token()) ?>

						<p>
							<label for="password1">
								<?= __('Enter a new password') ?>
							</label>
							<input type="password" placeholder='<?= __('Enter a new password') ?>' name="password1" required class="b-input" id="password1" />
						</p>

						<p>
							<label for="password2">
								<?= __('Re-enter your new password') ?>
							</label>
							<input type="password" placeholder='<?= __('Re-enter your new password') ?>' name="password2" required class="b-input" id="password2" />
						</p>

						<input type='submit' value='<?= __('Update my password') ?>' />
					<? else: ?>
						<p>
							Please enter your email address.
						</p>
						<p>
							An email will be sent to you with a link create a new password.
						</p>
						<p>
							<label for="email">
								<?= __('Email address') ?>
							</label>
							<input type="email" placeholder='<?= __('Email address') ?>' name="email" required class="b-input" id="email" />
						</p>

						<input type='submit' value='<?= __('Send me a reset link') ?>' />
					<? endif ?>
				</fieldset>
			</form>
		</div>
	</body>
</html>