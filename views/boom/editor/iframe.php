<?= $before_closing_head ?>
<?= Boom::include_css() ?>
</head>
<?= $body_tag ?>
<? if (Kohana::$environment !== Kohana::PRODUCTION): ?>
	<div id="b-environment">
		<p><?= $environment ?> site <? if ($branch): ?><br /><?= $branch ?><? endif ?></p>
	</div>
<? endif; ?>

<? if (Editor::instance()->state_is(Editor::EDIT)): ?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('body').css('margin-left', '60px');
		});
	</script>
<? endif ?>

<iframe frameBorder="0" style="position: fixed; left: 0; top: 0; bottom: 0; width: 60px; height: 100%; overflow: hidden; z-index: 10000; background: transparent; <? if (Editor::instance()->state() !== Editor::EDIT): ?>border: none; width: 100px; right: 0; <? endif; ?>" id='b-page-topbar' scrolling="no" src='/cms/editor/toolbar/<?= $page_id ?>'></iframe>