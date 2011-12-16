<?
	$newsletter_page = ORM::factory( 'page' )->with( 'version' )->where( 'internal_name', '=', 'newsletter' )->find();

	if ($newsletter_page->loaded())
	{
		?>
			<form id="newsletter" method="post" action="<?= $newsletter_page->getAbsoluteUri() ;?>#newsletter-form">
				<fieldset>
					<input type="hidden" value="newsletter" name="validation-rules"/>
					<input type="hidden" value="1" name="small-form"/>
					<legend><? echo __( 'Get our e-newsletter' ); ?></legend>
					<label for="yourname"><? echo __( 'Your name' ); ?>:</label>
					<input id="yourname" type="text" name="name"/>
					<label for="youremail"><? echo __( 'Email address' ); ?>:</label>
					<input id="youremail" type="text" name="email"/>
					<input id="submitform" type="submit" name="submit" value="Sign up"/>
				</fieldset>
			</form>
		<?
	}
?>
