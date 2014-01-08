	<?= View::factory('boom/header', array('title' =>	'Assets')); ?>

	<?= $manager ?>

	<?= Boom::include_js() ?>
	<script type="text/javascript">
		//<![CDATA[
		(function($){
			$.boom.init({
				csrf: '<?= Security::token() ?>'
			});

			$( 'body' ).ui().browser_asset({
				allowedUploadTypes:[ '<?= implode('\', \'', Boom_Asset::$allowed_extensions)?>' ]
			});
		})(jQuery);
		//]]>
	</script>
</body>
</html>