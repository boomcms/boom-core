<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title>CMS | <?= __('Login'); ?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<?= Assets::factory('boom_css')->css('cms.css.less') ?>
</head>
<body>
	<form action="/cms/login" method="post">
		<label>
			Email
			<input type="text" name="email" />
		</label>
		<label>
			Password
			<input type="password" name="password" />
		</label>

		<input type="submit" value="Submit" />
	</form>
</body>
</html>