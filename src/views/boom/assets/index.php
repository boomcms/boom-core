	<?= View::make('boom::header', ['title' => 'Assets']) ?>

	<?= $manager ?>

	<script type="text/javascript">
		//<![CDATA[
		(function ($) {
			$.boom.init();

			$('body')
				.ui()
				.assetManager({
					allowedUploadTypes:[ '<?= implode('\', \'', \Boom\Asset\Mimetype::$allowedExtensions) ?>' ]
				});
		})(jQuery);
		//]]>
	</script>
</body>
</html>
