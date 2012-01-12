<h1 id="logo">
	<a title="<?=Kohana::$config->load('config' )->get('client_name')?> Home" href="/">
		<? $page->get_slot( 'asset', 'logo' ); ?>
	</a>
</h1>
