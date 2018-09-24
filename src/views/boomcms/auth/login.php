<!DOCTYPE html>
<html dir="ltr" lang="en-gb" class="boom">
    <?= view('boomcms::head', ['title' => trans('boomcms::auth.login.title')]) ?>

    <body id='b-login'>
        <div>
            <div id="logo"></div>

            <form name="login-form" action="<?= URL::route('login') ?>" method="post">
                <?= csrf_field() ?>

                <fieldset>
                    <?php if (count($errors)): ?>
                        <p class="b-error"><?= implode('<br />', $errors->all()) ?></p>
                    <?php endif ?>

                    <span class="input">
                        <input type="email" name="email" required id="email" value="<?= old('email') ?>" />

						<label for="email">
                            <span><?= trans('boomcms::auth.login.email') ?></span>
						</label>

                        <span class="fa fa-at fa-2x"></span>
                    </span>

                    <span class="input">
                        <input type="password" name="password" required id="password" />
                   
                        <label for="password">
                            <span><?= trans('boomcms::auth.login.password') ?></span>
                        </label>

                        <span class="fa fa-key fa-2x"></span>
                    </span>

                    <p>
                        <label class="b-remember-me-label">
                            <input type="checkbox" name="remember" class="b-remember-me" value='1' />
                            <?= trans('boomcms::auth.login.remember') ?>
                        </label>
                    </p>

                    <input type='submit' value='<?= trans('boomcms::auth.login.title') ?>' />
                    <a id='b-login-recover-link' href='<?= URL::route('password') ?>'><?= trans('boomcms::auth.login.recover') ?></a>
                </fieldset>
            </form>
        </div>

        <?= view('boomcms::footer') ?>
