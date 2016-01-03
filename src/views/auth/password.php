<!DOCTYPE html>
<html dir="ltr" lang="en-gb" class="boom">
	<head>
		<title>BoomCMS | <?= trans('boomcms::auth.reset.title') ?></title>
		<meta name="robots" content="noindex, nofollow" />
		<link rel="stylesheet" type="text/css" href="/vendor/boomcms/boom-core/css/cms.css" />
	</head>

	<body id='b-login'>
		<div>
			<div id="logo"></div>

            <?php if (isset($status)): ?>
                <p><?= $status ?>
            <?php else: ?>
                <form name="login-form" action="/cms/recover" method="post">
                    <input type="hidden" name="_token" value="<?php echo csrf_token() ?>">

                    <fieldset>
                        <?php if (count($errors)): ?>
                            <p class="b-error"><?= implode('<br />', $errors->all()) ?></p>
                        <?php endif ?>

                        <p>
                            <?= trans('boomcms::auth.reset.enter-email') ?>
                        </p>

                        <p>
                            <?= trans('boomcms::auth.reset.what-will-happen') ?>
                        </p>

                        <p>
                            <label for="email">
                                <?= trans('boomcms::auth.reset.email') ?>
                            </label>
                            <input type="email" placeholder='<?= trans('boomcms::auth.reset.email') ?>' name="email" required id="email" />
                        </p>

                        <input type='submit' value='<?= trans('boomcms::auth.reset.send-link') ?>' />
                    </fieldset>
                </form>
            <?php endif ?>
		</div>
	</body>
</html>
