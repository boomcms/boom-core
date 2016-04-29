<!DOCTYPE html>
<html dir="ltr" lang="en-gb" class="boom">
	<head>
		<title>BoomCMS | <?= trans('Login') ?></title>
		<meta name="robots" content="noindex, nofollow" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="/vendor/boomcms/boom-core/css/cms.css" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    </head>

    <body id='b-login'>
        <div>
            <div id="logo"></div>

            <form name="login-form" action="<?= URL::route('login') ?>" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token() ?>">

                <fieldset>
                    <?php if (count($errors)): ?>
                        <p class="b-error"><?= implode('<br />', $errors->all()) ?></p>
                    <?php endif ?>

                    <span class="input">
                        <input type="email" name="email" required id="email" value="<?= old('email') ?>" />

						<label for="email">
                            <span><?= trans('boomcms::auth.login.email') ?></span>
						</label>

                        <span class="fa fa-at"></span>
                    </span>

                    <span class="input">
                        <input type="password" name="password" required id="password" />
                   
                        <label for="password">
                            <span><?= trans('boomcms::auth.login.password') ?></span>
                        </label>

                        <span class="fa fa-key"></span>
                    </span>

                    <p>
                        <label class="b-remember-me-label">
                            <input type="checkbox" name="remember" class="b-remember-me" value='1' />
                            <?= trans('Keep me signed in') ?> (<?= trans('until you log out') ?>)
                        </label>
                    </p>

                    <input type='submit' value='<?= trans('Login') ?>' />
                    <a id='b-login-recover-link' href='<?= URL::route('password') ?>'>I've forgotten my password</a>
                </fieldset>
            </form>
        </div>
    </body>
</html>
