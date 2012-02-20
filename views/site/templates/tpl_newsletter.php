<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<?
	form::nullpost(array('name', 'email', 'postcode', 'list_centre'));
	if (isset($_POST['submit']) and !isset($_POST['small-form'])) {
		if (!$_POST['list_centre']) {
			$_POST['list_centre'] = 'all';
		}
		Validation::form();
	}
?>
<div class="yui-t2 wrapper">
	<div class="padd">
	
	<div id="yui-main">
		<div class="yui-b">

			<?= new View('site/subtpl_siteheader');?>
		
			<div class="yui-gc">
					
				<div class="yui-g first">
					<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature1', 'right');?>
					<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature2', 'right');?>
				</div>
				
				<div class="yui-g">
					<h1 class="pageTitle" id='sledge-page-title'><?= $this->page->title?></h1>

					<?if (isset($_POST['submit']) and !isset($_POST['small-form']) and !sizeof($this->post_errors)){
						$list_name = '';
						if ($_POST['list_centre'] and $_POST['list_centre'] != 'all') {
							$list_name = "'".O::f($this->page_model)->find_by_internal_name($_POST['list_centre'])->title."'";
						}
						echo "<h2>Thank you. You are now subscribed to the {$list_name} Active Kids mailing list.</h2>";?>
					<?}else{?>
						<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'standfirst', '<h2 class="standFirst">', '</h2>','ch,ins'); ?>
						<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'bodycopy', '<div id="content" class="cleafix">', '</div>');?>
						<a name="newsletter-form"></a>
						<form method="post" action="<?-$this->page->absolute_uri()?>" class="contactform">
							<fieldset>
								<input type="hidden" name="validation-rules" value="newsletter" />
								<p>
									<span class="label">
										<label for="name">
											Your name
											<span class="required">
												&nbsp;
											</span>
										</label>
										<?if(isset($this->post_errors['name'])) {?>
											<span class="error"><?=$this->post_errors['name'];?></span>
										<?}?>
									</span>
									<?=form::input('name',$_POST['name'],' class="textinput"');?>
								</p>
								<p>
									<span class="label">
										<label for="email">
											Email address
											<span class="required">
												&nbsp;
											</span>
										</label>
										<?if(isset($this->post_errors['email'])) {?>
											<span class="error"><?=$this->post_errors['email'];?></span>
										<?}?>
									</span>
									<?=form::input('email',$_POST['email'],' class="textinput"');?>
								</p>
								<p>
									<button type="submit" name="submit" class="button">Send</button>
								</p>
							</fieldset>
						</form>
						<script type="text/javascript">
							$("#name").focus();
						</script>
					<?}?>
				</div>
			</div>
		</div>
	</div>

	<!-- left secondary block -->
	<div class="yui-b">
		<h1 id="logo">
			<a title="<?=Kohana::config('core.clientnamelong')?> home" href="/"><img src="/sledge/img/main_logo.jpg" alt="home" /></a>
		</h1>
			
		<?= new View('site/subtpl_leftnav'); ?>		
	</div>
	<?= new View('site/subtpl_footer'); ?>
	</div>
</div>
