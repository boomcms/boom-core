<?php
/*
 * Display and edit a user's account details (name, password).
 * @package Template
 * @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
 * @copyright 2011, Hoop Associates
 *
 * Rendered by Controller_Cms_Account::action_profile()
 * Submits to Controller_Cms_Account::action_profile()
 *
 * Variables:
 *	$actual_person Instance of Model_Person - The current logged in user.
 *	$people Array of Model_Person objects - For selecting a user to mimick
 */
?>

<p class="information">
	You can use this form to update your account details. If you leave the password fields blank your password will not be changed.
</p>
<div style="margin-top: 20px;">
	<form id="update-details-form" onsubmit='return false;'>
		<table>
			<?
				if ($actual_person->can( 'manage people' )):
					?>
						<tr>
							<td>
								Real user:
							</td>
							<td>
								<?= $actual_person->getName() ?>
							</td>
						</tr>
						<tr>
							<td><label for="switch_user">Mimic user:</label></td>
							<td><select name="switch_user" id="switch_user">
								<option value="">- Select a user to mimic -</option>
								<?
									foreach ($people as $p):
										if ($p->id !== $actual_person->id && $p->id !== $person->id):
											echo "<option value='$p->id'";
						
											if ($p->id == $person->rid) 
												echo 'selected="selected"'; 
							
											echo ">", $p->getName(), "</option>";
										endif;
									endforeach;
								?>
							</td>
						</tr>
						<tr>
							<td></td>
							<td>
								<span id="switch_user-error" style="color: #f00;">&nbsp;</span>
							</td>
						</tr>
					<?
				endif;
			?>
			<tr>
				<td><label for="firstname">First name:</label></td>
				<td><?= Form::input('firstname', $actual_person->firstname, array('size' => 35 )) ?></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<span id="firstname-error" style="color: #f00;">&nbsp;</span>
				</td>
			</tr>
			<tr>
				<td><label for="lastname">Last name:</label></td>
				<td><?= Form::input('lastname', $actual_person->lastname, array('size' => 35 )) ?></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<span id="lastname-error" style="color: #f00;">&nbsp;</span>
				</td>
			</tr>
			<tr>
				<td><label for="password">Password:</label></td>
				<td><?= Form::password('password','', array( 'size' => 35 ))?></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<span id="password-error" style="color: #f00;">&nbsp;</span>
				</td>
			</tr>
			<tr>
				<td><label for="confirm">Confirm password:</label></td>
				<td><?= Form::password('confirm','', array( 'size' => 35 ))?></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<span id="confirm-error" style="color: #f00;">&nbsp;</span>
				</td>
			</tr>
		</table>
	</form>
</div>