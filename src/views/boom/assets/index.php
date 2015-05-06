	<?= View::make('header', ['title' =>    'Assets']); ?>

	<?= $manager ?>

	<?= Boom::include_js() ?>
	<script type="text/javascript">
		//<![CDATA[
		(function ($) {
			$.boom.init({
				csrf: '<?= Security::token() ?>'
			});

			$('body')
				.ui()
				.assetManager({
					allowedUploadTypes:[ '<?= implode('\', \'', \Boom\Asset\Mimetype::$allowedExtensions)?>' ]
				});
		})(jQuery);
		//]]>
	</script>
</body>
</html>
