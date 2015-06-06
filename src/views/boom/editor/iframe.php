    <?= $before_closing_head ?>
    <link rel="stylesheet" type="text/css" href="/vendor/boomcms/boom-core/css/cms.css" />
</head>
<?= $body_tag ?>

<?php if ($editor->isEnabled()): ?>
	<script type="text/javascript">
		(function () {
			document.getElementsByTagName("BODY")[0].style['margin-left'] = "60px";
		})();
	</script>
<?php endif ?>

<iframe frameBorder="0" style="max-width: none; position: fixed; left: 0; top: 0; bottom: 0; width: 60px; height: <?= $editor->isEnabled() ? '100%' : '35px' ?>; overflow: hidden; z-index: 10000; background: transparent" id='b-page-topbar' scrolling="no" src='/cms/editor/toolbar?page_id=<?= $page_id ?>'></iframe>
