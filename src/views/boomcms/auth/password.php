<!DOCTYPE html>
<html dir="ltr" lang="en-gb" class="boom">
	<head>
		<title>BoomCMS | <?= trans('boomcms::auth.reset.title') ?></title>
		<meta name="robots" content="noindex, nofollow" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="/vendor/boomcms/boom-core/css/cms.css" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    </head>

	<body id='b-login'>
		<div>
			<div id="logo"></div>

            <?php if (session('status')): ?>
                <p><?= session('status') ?>
            <?php else: ?>
                <form name="login-form" action="<?= URL::route('password') ?>" method="post">
                    <input type="hidden" name="_token" value="<?php echo csrf_token() ?>">

                    <fieldset>
                        <?php if (count($errors)): ?>
                            <p class="b-error"><?= implode('<br />', $errors->all()) ?></p>
                        <?php endif ?>

                        <p>
                            <?= trans('boomcms::auth.reset.enter-email') ?>
                            <?= trans('boomcms::auth.reset.what-will-happen') ?>
                        </p>

                        <span class="input">
                            <input type="email" name="email" required id="email" autofocus />

                            <label for="email">
                                <span><?= trans('boomcms::auth.reset.email') ?></span>
                            </label>

                            <span class="fa fa-at"></span>
                        </span>

                        <input type='submit' value='<?= trans('boomcms::auth.reset.send-link') ?>' />
                    </fieldset>
                </form>
            <?php endif ?>
		</div>
	</body>
</html>
