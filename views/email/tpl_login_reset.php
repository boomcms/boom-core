<?php
/**
* Email template for CMS password reset emails.
*
* Rendered by Controller_Cms_Account::action_forgotten().
*
*********************** Variables **********************
*	$person		****	instance of Model_Person	****	The dimwit who's forgotten their password.
********************************************************
*
* @uses URL::base()
* @uses Request::current()
*/
?>

Dear <?=$person->getName()?>,

You have requested a new password for <?= URL::base( Request::current() ) ?>cms/login

Your new password is: <?=$password ?>


Please keep this new password in a secure location.

Login to the CMS at <?= URL::base( Request::current() ) ?>cms/login?email=<?=$person->emailaddress?>

Thank you.  
