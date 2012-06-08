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

In order to create a new password for your CMS account please <a href="<?= URL::site( '/cms/account/forgotten' ) ?>?email=<?= $person->emailaddress ?>&token=<?= $token ?>">click here</a>.

This link will be valid for one hour.

Thank you.  
