	<?= View::factory('boom/header', array('title' =>	'Assets')); ?>

	<?= $manager ?>

	<?= Boom::include_js() ?>
	<script type="text/javascript">
		//<![CDATA[
		(function($){
			$.boom.init({
				csrf: '<?= Security::token() ?>'
			});

			$('body')
				.ui()
				.assetManager({
					allowedUploadTypes:[ '<?= implode('\', \'', \Boom\Asset::$allowed_extensions)?>' ]
				});
		})(jQuery);
		//]]>
	</script>
</body>
</html>