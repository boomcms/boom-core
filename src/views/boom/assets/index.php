	<?= View::make('boom::header', ['title' => 'Assets']) ?>

	<?= $manager ?>

	<script type="text/javascript">
		//<![CDATA[
		(function ($) {
			$.boom.init();

			$('body')
				.ui()
				.assetManager({
					allowedUploadTypes:[ '<?= implode('\', \'', \BoomCMS\Core\Asset\Mimetype\Mimetype::$allowedExtensions) ?>' ]
				});
		})(jQuery);
		//]]>
	</script>
</body>
</html>
