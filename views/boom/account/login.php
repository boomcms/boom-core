<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title>CMS | <?= __('Login'); ?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<meta name="robots" content="noindex, nofollow" />
	<?= Boom::include_css() ?>
</head>
<body>
	<div id="b-login-form">
		<div class="ui-widget-shadow"></div>
		<div class="b-login-form-content">
			<div class="b-margin boom-tabs">
				<ul class="">
					<li class=""><a href="#tab-login">Login</a></li>
					<li class=""><a href="#tab-reset"><?= __('Password reset') ?></a></li>
				</ul>
				<div id="tab-login" class="ui-tabs-panel">
					<form id="login-form" name="login-form" action="/cms/login" method="post">
						<?= Form::hidden('csrf', Security::token()) ?>
						<fieldset>
							<br />
							<? if (isset($login_error)): ?>
								<p class="b-error"><?= $login_error ?></p>
							<? endif ?>
							<p>
								<label for="email">
									<?= __('Email address') ?>
								</label>
								<input type="text" name="email" class="b-input" id="email" value="<?= $request->query('email') ?>" />
							</p>
							<p>
								<label for="password">
									<?= __('Password') ?>
								</label>
								<input type="password" name="password" class="b-input" id="password" />
							</p>
							<p>
								<label class="b-remember-me-label">
									<input type="checkbox" name="remember" class="b-remember-me" />
									<?= __('Keep me signed in') ?> (<?= __('until you log out') ?>)
								</label>
							</p>
							<p class="ui-helper-clearfix">
								<label>&nbsp;</label>
								<button class="boom-button ui-helper-left ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit">
									<span class="ui-button-text">
										<?= __('Log in') ?>
									</span>
								</button>
							</p>
							<p class="ui-helper-left">
								<a href="/cms/login/openid">Alternative login</a>
							</p>
						</fieldset>
					</form>
				</div>
				<div id="tab-reset" class="ui-tabs-panel">
					<form id="reset-form" name="reset-form" action="/cms/recover<? if (isset($token)): ?>?token=<?= $token->token ?>&email=<?= $email ?><? endif ?>" method="post">
						<?= Form::hidden('csrf', Security::token()) ?>

						<? if (isset($token)): ?>
							<fieldset>
								<p class="b-error ui-helper-hidden"></p>
								<p><?= __('Use this form to reset your password') ?>.</p>

								<p>
									<label for="password1">
										<?= __('New password') ?>
									</label>
									<input type="password" name="password1" class="b-input" id="password1" value="" />
								</p>
								<p>
									<label for="password2">
										<?= __('Confirm password') ?>
									</label>
									<input type="password" name="password2" class="b-input" id="password2" value="" />
								</p>
								<p class="ui-helper-clearfix">
									<label>&nbsp;</label>
									<button class="boom-button ui-helper-left ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit">
										<span class="ui-button-text">
											<?= __('Change password') ?>
										</span>
									</button>
								</p>
							</fieldset>
						<? else: ?>
							<fieldset>
								<? if (isset($error)): ?>
									<p class="b-error ui-state-error ui-corner-all"><?= $error ?></p>
								<? elseif (isset($message)): ?>
									<p class="b-success ui-state-highlight ui-corner-all"><?= $message ?></p>
								<? else: ?>
									<p><?= __('After you submit this form you will receive an email with instructions for resetting your password') ?>.</p>

									<p>
										<label for="email">
											<?= __('Email address') ?>
										</label>
										<input type="text" name="email" class="b-input" id="reset-email" value="<?= $request->query('email') ?>" />
									</p>
									<p class="ui-helper-clearfix">
										<button class="boom-button ui-helper-left ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit">
											<span class="ui-button-text">
												<?= __('Reset password') ?>
											</span>
										</button>
									</p>
								<? endif ?>
							</fieldset>
						<? endif; ?>
					</form>
				</div>
			</div>
		</div>
	</div>

	<?= Boom::include_js() ?>
	<script type="text/javascript">
		//<![CDATA[
		(function($){
			$.boom.init();

			<? if (isset($tab)): ?>
				$("a[href='#tab-<?= $tab ?>']").trigger('click');
			<? endif ?>
		})(jQuery);
		//]]>
	</script>
</body>
</html>