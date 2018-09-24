<!DOCTYPE html>
<html dir="ltr" lang="en-gb" class="boom">
    <?= view('boomcms::head', ['title' => trans('boomcms::auth.reset.title')]) ?>

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

                            <span class="fa fa-at fa-2x"></span>
                        </span>

                        <input type='submit' value='<?= trans('boomcms::auth.reset.send-link') ?>' />
                    </fieldset>
                </form>
            <?php endif ?>
		</div>

        <?= view('boomcms::footer') ?>
