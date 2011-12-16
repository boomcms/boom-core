<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<? 
	$comments = $this->page->get_comments();
?>
<big>
	<strong>
		Comments (<?=count($comments);?>)
	</strong>
</big>
<?if (count($comments)){?>
	<ul class="commentlist">
		<? foreach ($comments as $comment) {?>
		<li>
			<div class="metadata">
				<span class="date">
					<?=$comment->datetime?>
				</span> 
				- 
				<em>
						<?=$comment->name?>
				</em> says:
				<? if (@$can_delete) {?>
					<a href="<?=$this->page->absolute_uri()?>?deletecomment=<?=$comment->id?>">[Delete]</a>
				<?}?>
			</div>
			<p>
				<?=nl2br($comment->comment)?>
			</p>
		</li>
		<?}?>
	</ul>
<?} else {?>
<p>
	<em>(No comments)</em>
</p>
<?}?>
<a name="post-comment"></a>
<h2>
	<a href="#post-comment" onclick="$('.commentform').toggle()">
		Post comment +
	</a>
</h2>
<form method="post" action="<?=$this->page->absolute_uri();?>#post-comment" class="<?=(isset($this->post_errors) and count($this->post_errors))?'':'hidden ';?>commentform">
	<fieldset>
		<input type="hidden" name="validation-rules" value="postcomment" />
		<ul>
			<li>
				<label for="name">
					name
					<span class="required">
						*
					</span>
					<? if (isset($this->post_errors['name'])) {?>
						<span class="error">
							<?=strtolower($this->post_errors['name'])?>
						</span>
					<?}?>
				</label>
				<input type="text" name="name" id="name" class="textinput-long" value="<?=$_POST['name']?>" />
			</li>
			<li>
				<label for="email">
					email
					<span class="required">
						*
					</span>
					<? if (isset($this->post_errors['email'])) {?>
						<span class="error">
							<?=strtolower($this->post_errors['email'])?>
						</span>
					<?}?>
				</label>
				<input type="text" name="email" id="email" class="textinput-long" value="<?=$_POST['email']?>" />
			</li>
			<li>
				<label for="comment">
					comment
					<span class="required">
						*
					</span>
					<? if (isset($this->post_errors['comment'])) {?>
						<span class="error">
							<?=strtolower($this->post_errors['comment'])?>
							</span>
						<?}?>
					</label>
					<textarea name="comment" id="comment" rows="5" cols="20"><?=$_POST['comment']?></textarea>
			</li>
			<li>
				<?=$this->captcha->render()?>
			</li>
			<li class="clearfix">
				<label for="captcha">
					please enter the letters from the image
					<span class="required">
						*
					</span>
				</label>
				<input type="text" name="captcha" id="captcha" class="textinput-captcha" />
				<? if (isset($this->post_errors['captcha'])) {?>
					<span class="error">
						<?=strtolower($this->post_errors['captcha'])?>
					</span>
				<?}?>
			</li>
			<li>
				<input class="button-3" type="submit" value="Submit" name="submit"/>
			</li>
		</ul>
	</fieldset>
</form>
