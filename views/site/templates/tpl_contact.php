<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>

<div class="wrapper">
	<?= new View('site/subtpl_siteheader');?>		
	<div id="navigation">
		<?= new View('site/subtpl_logo');?>
		<?= new View('site/nav/left'); ?>
		<?= new View('site/subtpl_newsletter');?>
	</div>
	<div id="main-content">
		<div class="headings">
			<h1 class="pageTitle"><?= $page->title?></h1>
			<?= $page->getSlot('text', 'standfirst', '<h2 class="standFirst">', '</h2>','ch,ins'); ?>
		</div>
		<p>
			<?= __('Fields marked with a') ?> <span class="required">*</span> <?= __('are required') ?>.
		</p>
		<form method="post" action="<?= $request->uri() ;?>" class="contactform">
			<input type="hidden" name="validation-rules" value="companycontact" />
			<fieldset>
				<ul>
					<li>
						<label for="name">
							<span class="label">
								<?= __('Name') ?>
								<span class="required">
									*
								</span>
							</span>
						</label>
						<?=Form::input('name', Arr::get( $_POST, 'name' ));?>
					</li>
					<li>
						<label for="email">
							<span class="label">
								<?= __('Email') ?>
								<span class="required">
									*
								</span>
							</span>
						</label>
						<?=Form::input('email', Arr::get( $_POST, 'email' ));?>
					</li>
					<li>
						<label for="message">
							<?= __('Message' ) ?>:
							<span class="required">
								*
							</span>
						</label>
						<?=Form::textarea('message', Arr::get( $_POST, 'message' ), array( 'cols' => 40, 'row' => 5) );?>
					</li>
					<? 
						if (isset($post_errors['message']))
						{ 
							?>
								<li>
									<span class="error"><?=$post_errors['message'];?></span>
								</li>
							<? 
						}
					?>
					<li>
						<input name="submit" type="submit" value="Submit" class="button" />
					</li>
				</ul>
			</fieldset>
			</form>			
		</div>
	</div>
	<div id="aside">
		<?= $page->getSlot('feature', 'feature1', 'right');?>
		<?= $page->getSlot('feature', 'feature2', 'right');?>
	</div>
	<?= new View('site/subtpl_footer'); ?>
</div>
