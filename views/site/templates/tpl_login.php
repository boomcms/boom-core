<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>

<div id="cmswrapper" class="wrapper">
	<div class="headings">
		<h1 class="pageTitle"><?= $this->page->title?></h1>
		<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'standfirst', '<h2 class="standFirst">', '</h2>','ch,ins'); ?>
	</div>
	<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'bodycopy', '<div id="content">', '</div>');?>

	<div id="loginbar">
		<p class="message error"><?if (isset($this->msg)) {echo $this->msg;}?></p>

		<form action="/cms/login" method="post" class="loginform" id="loginform">
			<fieldset>
				<ul>
					<?if (file_exists("/etc/apache2/ssl/client.crt") && @$_SERVER['SSL_CLIENT_CERT'] == @file_get_contents("/etc/apache2/ssl/client.crt")) {?>
						<li id="superuser_mode_text">
								Client certificate verified - superuser mode enabled.
						</li>
						<li>
							<label for="disable_superuser_mode">Disable superuser mode (for testing logins)</label>
							<input id="disable_superuser_mode" type="checkbox" name="disable_superuser_mode" value="1" />
						</li>
					<?}?>
					<!--email-->
					<li class="first-field">
						<!--fieldname, asterisk-of-requirement, input element-->
						<label for="email" class="fields">
							<!--fieldname-->
							Email address: 
							<!--asterisk-of-requirement-->
								<span class="required">*</span>
							<!--input element-->
						</label>
						<?=form::input('email',@$_POST['email'],' class="cmsinput" tabindex="1"')?>
						<!--errors--> 
						<label for="email" class="fieldserror">&nbsp;</label>	
					</li>
					<!--password-->
					<li>
						<!--<input type="submit" id="submit" name="submit" value="Login" />	-->
						<!--fieldname, asterisk-of-requirement, input element-->
						<label for="password" class="fields">
							<!--fieldname-->
							Password: 
								<!--asterisk-of-requirement-->
								<span class="required">*</span>
							<!--input element-->
						</label>
						<?=form::password('password','',' class="cmsinput" tabindex="2"')?>
					</li>
					<li>
						<!--input element-->
						<a id="add_ext_link" tabindex="3" class="button confirm_yes right" href="#login" onclick="$('#loginform')[0].submit()">
							<span class="left">&nbsp;</span>
							<span class="center">Login</span>
							<span class="right">&nbsp;</span>
						</a>
						<!--errors-->
						<label for="password" class="fieldserror">&nbsp;</label>
					</li>
					<li>
						<span id="capslock" class="error"></span>
						<a tabindex="4" href="#reset">Reset the password</a>
					</li>
				</ul>
			</fieldset>
		</form>	
		
		<script type="text/javascript">
		$(function(){
			
			if ($("#email").val() == "") {
				$("#email").focus();
			} else if ($("#password").val() == '') {
				$("#password").focus();
			} else {
				$("#add_ext_link").focus();
			}
			// reset the password link click
			$("a[href='#reset']").click(function(e){
				e.preventDefault();
				location.href = '/cms/login/reset?email=' + document.getElementById('loginform').email.value;
			});
			var prevKey = 0;
			// listen for enter key, submit form only when previous keys pressed were not up or down
			$("#email, #password").keyup(function(e) {
				if (e.keyCode == 13 && prevKey != 40 && prevKey != 38) {
					$("#loginform").submit();
				} else {
					if (e.target.id == "password" && e.shiftKey == false && e.keyCode >= 60) {
						var char = this.value.charAt(this.value.length-1);
						if (char == char.toUpperCase()) {
							//$("#capslock").html("Caps lock is on! &nbsp;&nbsp;").fadeIn();
						} else {
							//$("#capslock").fadeOut();
						}
					} 
					prevKey = e.keyCode;
				}
			});
		});
		</script>

		<h2>Warning</h2>
		<p>This is a private system, for use by <?=Kohana::config('core.clientnamelong')?> &amp; Hoop Associates.</p>

		<p>Unauthorised access to this system is forbidden and will be prosecuted by law.</p>

		<p>By accessing this system, you agree that your actions may be monitored if unauthorised usage is suspected. </p>

		<h2>Need a hand?</h2>
		<p>
			<address>
				<em>Hoop Associates</em><br />
				34-35 Great Sutton Street<br />
				London EC1V 0DX<br />
			</address>
		</p>
		<p>
			<address>
				<em>Telephone</em> 020 7690 5431
			</address>
		</p>

		<p>
			<address>
				<em>Facsimile</em> 020 7690 5433
			</address>
		</p>
		
		<p>
			<address>
				<em>Email</em> <a href="mailto:mail@hoopassociates.co.uk">mail@hoopassociates.co.uk</a>
			</address>
		</p>
	</div>

</div>
<script type="text/javascript">
	if ($('#disable_superuser_mode').length >0) {
		$('#disable_superuser_mode').click(function(){
			if (this.checked) {
				$('#superuser_mode_text')[0].innerHTML = '<span style="color: #ff0000;">Client certificate verified - superuser mode disabled.</span>';
			} else {
				$('#superuser_mode_text')[0].innerHTML = '<span style="color: #008800;">Client certificate verified - superuser mode enabled.</span>';
			}
		});
	}
</script>
