    <?= $before_closing_head ?>
</head>
<?= $body_tag ?>

<?php if ($editor->isEnabled() || $editor->isHistory()): ?>
	<script type="text/javascript">
		(function () {
			document.getElementsByTagName("BODY")[0].style['margin-left'] = "<?= $editor->isEnabled() ? '60px' : '101px' ?>";
		})();
	</script>
<?php endif ?>

<script src="/vendor/boomcms/boom-core/js/tinymce.js"></script>
<link rel="stylesheet" type="text/css" href="/vendor/boomcms/boom-core/css/inpage.css">
<iframe id="b-editor-iframe" data-state="<?= $editor->getState() ?>" src='/boomcms/editor/toolbar?page_id=<?= $page_id ?>'></iframe>
