<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<? 

if(isset($_POST['submit'])) {
	Validation::form();
}

form::nullpost(array('name', 'email', 'message'));
?>
<div class="wrapper">
	<?= new View('site/subtpl_siteheader');?>		
	<div id="navigation">
		<?= new View('site/subtpl_logo');?>
		<?= new View('site/subtpl_leftnav'); ?>
		<?= new View('site/subtpl_newsletter');?>
	</div>
	<div id="main-content">
		<div class="headings">
			<h1 class="pageTitle"><?= $this->page->title?></h1>
			<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'standfirst', '<h2 class="standFirst">', '</h2>','ch,ins'); ?>
		</div>
		<? if(!isset($_POST['submit'])) {
			echo O::f('chunk_text_v')->get_chunk($this->page->rid, 'bodycopy', '<div id="content">', '</div>');
		}?>
		<?
			if(isset($_POST['submit']) && !$this->has_errors) { ?>
				<p>Thank you for contacting Hoop Associates.</p>
				<p>Where a response is required a member of staff will be in touch to advise that your enquiry has been received and is being dealt with.</p>
				<p>If you have an urgent query, or would like to speak with us directly, please call us on 020 7690 5431. Alternatively you can email us at support@hoopassociates.co.uk.</p>
		<? } else { ?>
		<p>
			Fields marked with a <span class="required">*</span> are required.
		</p>
		<? if(isset($_POST['submit']) && $this->has_errors) { ?>
			<p>
				<span class="error">There were some errors with your submission, please correct the items in red below.</span>
			</p>
				<? } ?>
				<form method="post" action="<?=str_replace('/index.php/','',$_SERVER['PHP_SELF']);?>" class="contactform">
					<input type="hidden" name="validation-rules" value="companycontact" />
					<fieldset>
						<ul>
							<li>
								<label for="name">
									<span class="label">
										Name
										<span class="required">
											*
										</span>
										<? if(isset($this->post_errors['name'])) { ?>
											<span class="error"><?=$this->post_errors['name'];?></span>
										<? } ?>
									</span>
								</label>
								<?=form::input('name',$_POST['name']);?>
							</li>
							<li>
								<label for="email">
									<span class="label">
										Email
										<span class="required">
											*
										</span>
										<? if(isset($this->post_errors['email'])) { ?>
											<span class="error"><?=$this->post_errors['email'];?></span>
										<? } ?>
									</span>
								</label>
								<?=form::input('email',$_POST['email']);?>
							</li>
							<li>
								<label for="message">
									Message:
									<span class="required">
										*
									</span>
								</label>
								<?=form::textarea('message',$_POST['message'],'cols="40" rows="5"');?>
							</li>
							<? if(isset($this->post_errors['message'])) { ?>
								<li>
									<span class="error"><?=$this->post_errors['message'];?></span>
								</li>
							<? } ?>
							<? if(strstr($_SERVER['SCRIPT_URI'],'/cms_page_manager/')==FALSE) { ?>
								<li>
									<input name="submit" type="submit" value="Submit" class="button" />
								</li>
							<?}?>
						</ul>
					</fieldset>
			</form>			
		<?}?>
		</div>
	</div>
	<div id="aside">
		<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature1', 'right');?>
		<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature2', 'right');?>
	</div>
	<?= new View('site/subtpl_footer'); ?>
</div>
