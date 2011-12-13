<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<?
	form::nullpost(array('name', 'email', 'comment'));
	$this->captcha = new Captcha;
	$can_delete = Permissions::may_i("Write",$this->page);

	// delete comment       
	if (isset($_GET['deletecomment']) and $can_delete) {
		$this->page->delete_comment($_GET['deletecomment']);
	}

	// save comment
	if (isset($_POST['submit'])) {
		Validation::form();

		if (!Captcha::valid($_POST['captcha'])) {
			$this->has_errors = true;
			$this->post_errors['captcha'] = 'Invalid captcha text';
		} elseif (!$this->has_errors) {
			$comment = new Comment(0, $_POST['name'], $_POST['email'], $_POST['comment'], false);
			$comment->save($this->page, false);
			// remove field values from post
			$_POST = array();
			form::nullpost(array('name', 'email', 'comment'));
		}
	}
?>
<div class="wrapper">
	<?= new View('site/subtpl_siteheader');?>
	<div id="navigation"> 
		<?= new View('site/subtpl_logo');?>
		<?= new View('site/subtpl_leftnav'); ?>		
	</div>
	<div id="main-content"> 	
		<div class="headings">
			<h1 class="pageTitle"><?= $this->page->title?></h1>
			<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'standfirst', '<h2 class="standFirst">', '</h2>','ch,ins'); ?>
		</div>
		<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'bodycopy', '<div id="content" class="clearfix">', '</div>');?>

		<div class="metadata">
			<?= new View('site/subtpl_blog_post_metadata');?>
		</div>
		<div class="comments">
			<?= new View('site/subtpl_blog_comments');?>
		</div>
	</div>
	<div id="aside">
		<?= new View('site/subtpl_archived_blogposts');?>
	</div>	
	<?= new View('site/subtpl_footer'); ?>
</div>
