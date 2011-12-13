<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta name="viewport" content="width=800" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Language" content="en-gb" />
		<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
		<title>Something's not working quite right</title>
		<link rel="stylesheet" href="/sledge/css/site_error_baseline.css" media="screen" />
		<link rel="stylesheet" href="/sledge/css/site_error_main.css" media="screen" />
		<script type="text/javascript" src="/sledge/js/jquery.js"></script>
		<script type="text/javascript" src="/sledge/js/cms_issue_tracker_basic.js"></script>
	</head>
	<body>
		<div id="broken" class="clearfix">
			<div class="column">
				<div class="warning">
					<h1>Unauthorised</h1>
				</div>
				<p>We're very sorry but you do not have permission to perform the requested action.</p>
				<p>This could mean that your login session has timed out, in which case you can log back in by <a href="/cms/login">clicking here</a>.</p>
			</div>
		</div>
		<?if (isset($timestamp_token)) {?>
		<div id="help">
			<div class="column">
				<h2>Contacting support</h2>
				<div style="height: 20px;"></div>
				<?if (Kohana::config('issuetracker.enable')) {?>
					<form method="post" action="#" style="width: 450px; margin-right: auto; margin-left: auto;" id="report-problem-form">
						<input type="hidden" name="timestamp-token" id="timestamp-token" value="<?=$timestamp_token?>" />
						<table>
							<tr><td><p style="margin: 0;">Please enter a subject line for your message to us.</p></td></tr>
							<tr><td><?=form::input('subject','','size="50"')?></td></tr>
							<tr><td><p style="margin: 0;"><span style="color: #f00;" id="subject-error">&nbsp;</span></p></td></tr>
							<tr><td><p style="margin: 0;">Please describe the action you took before receiving this error message.</p></td></tr>
							<tr><td><?=form::textarea('problem','','rows="8" cols="50"')?></td></tr>
							<tr><td><p style="margin: 0;"><span style="color: #f00;" id="problem-error">&nbsp;</span></p></td></tr>
							<tr><td>&nbsp;</td></tr>
							<tr><td><?=form::submit('submit','Send problem report')?></td></tr>
						</table>
					</form>
					<p id="problem-report-sent" style="display: none;">
						<span style="color: #f00;">Thanks!	We have received your problem report and will look into it as soon as we can.</span>
					</p>
				<?}?>
				<p class="code">Code: <?=$timestamp_token?></p>
				<p>If you feel you have received this message in error, please let us know so that we can look into it for you.</p>
				<p>You can contact us through <a href="http://hoop.basecamphq.com">HoopBase</a> 
				quoting the code above and let us know any relevant details. For example, where you 
				were in the website, what you were doing, what happened on screen etc. If you don't 
				have access to HoopBase, email <a 
				href="mailto:support@hoopassociates.co.uk">support@hoopassociates.co.uk</a> or call 
				us at +44 20 7690 5431.</p>
				<?if (class_exists("Kohana")) {?>
					<p class="return"><a href="/">Return to <?= Kohana::config('core.clientnamelong')?> homepage</a></p>
				<?}else{?>
				<?}?>
			</div>
		</div>
		<?}?>
	</body>
</html>
