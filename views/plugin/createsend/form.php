<? if (isset( $cs_error )): ?>
	<p>
		<?= $cs_error ?>
	</p>
<? endif; ?>

<form method="post" action="<?= ORM::factory('page')->where('internal_name', '=', 'newsletter')->find()->url(); ?>" class="site-form">
	<fieldset>
			<p>
				<span class="label">
					<label for="name">
						Your name
						<span class="required">*</span>
						<?
							if (isset($errors['name'])):
								echo "<span class='error'>", $errors['name'], "</span>";
							endif;
						?>
					</label>
				</span>
				<?=form::input('name', $request->post('name'), array('class' => 'textinput corner-5'));?>
			</p>
			<p>	
				<span class="label">
					<label for="email">
						Email address
						<span class="required">*</span>
						<?
							if (isset($errors['email'])):
								echo "<span class='error'>", $errors['email'], "</span>";
							endif;
						?>
					</label>
				</span>
				<?=form::input('email', $request->post('email'), array('class' => 'textinput corner-5') );?>
			</p>
			<p>
				<button type="submit" name="submit" class="button button-3 corner-5">Subscribe</button>
			</p>
	</fieldset>
</form>
<script type="text/javascript">
	$("#name").focus();
</script>