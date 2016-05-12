<!DOCTYPE html>
<html dir="ltr" lang="en-gb" class="boom">
    <?= view('boomcms::head', ['title' => trans('boomcms::auth.reset.title')]) ?>

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

                    <span class="input">
                        <input type="email" name="email" required id="email" autofocus />

                        <label for="email">
                            <span><?= trans('boomcms::auth.reset.email') ?></span>
                        </label>

                        <span class="fa fa-at"></span>
                    </span>

                    <span class="input">
                        <input type="password" name="password" required id="password" />
                   
                        <label for="password">
                            <span><?= trans('boomcms::auth.reset.password1') ?></span>
                        </label>

                        <span class="fa fa-key"></span>
                    </span>

                    <span class="input">
                        <input type="password" name="password_confirmation" required id="password_confirmation" />
                   
                        <label for="password_confirmation">
                            <span><?= trans('boomcms::auth.reset.password2') ?></span>
                        </label>

                        <span class="fa fa-key"></span>
                    </span>

                    <input type='submit' value='<?= trans('boomcms::auth.reset.update') ?>' />
                </fieldset>
            </form>
        </div>

        <?= view('boomcms::footer') ?>
