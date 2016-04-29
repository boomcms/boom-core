<!DOCTYPE html>
<html dir="ltr" lang="en-gb" class="boom">
	<head>
		<title>BoomCMS | <?= trans('boomcms::auth.reset.title') ?></title>
		<meta name="robots" content="noindex, nofollow" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="/vendor/boomcms/boom-core/css/cms.css" />
	</head>

	<body id='b-login'>
		<div>
			<div id="logo"></div>

			<form name="login-form" action="<?= URL::route('password'), "/$token" ?>" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token() ?>">
                <input type="hidden" name="token" value="<?= $token ?>" />
				<fieldset>
                    <legend>
                        <?= trans('boomcms::auth.reset.intro') ?>
                    </legend>

					<?php if (count($errors)): ?>
						<p class="b-error"><?= implode('<br />', $errors->all()) ?></p>
					<?php endif ?>

                    <p>
                        <label for="email">
                            <?= trans('boomcms::auth.reset.email') ?>
                        </label>
                        <input type="email" placeholder='<?= trans('boomcms::auth.reset.email') ?>' name="email" required id="email" />
                    </p>

                    <p>
                        <label for="password">
                            <?= trans('boomcms::auth.reset.password1') ?>
                        </label>
                        <input type="password" placeholder='<?= trans('boomcms::auth.reset.password1') ?>' name="password" required id="password" />
                    </p>

                    <p>
                        <label for="password_confirmation">
                            <?= trans('boomcms::auth.reset.password2') ?>
                        </label>
                        <input type="password" placeholder='<?= trans('boomcms::auth.reset.password2') ?>' name="password_confirmation" required id="password_confirmation" />
                    </p>

                    <input type='submit' value='<?= trans('boomcms::auth.reset.update') ?>' />
				</fieldset>
			</form>
		</div>
	</body>
</html>
