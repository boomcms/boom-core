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

Dear <?=$person->name()?>,

In order to create a new password for your CMS account please go to <a href="<?= URL::site('/cms/account/reset', Request::current()) ?>?email=<?= $person->emailaddress ?>&token=<?= $token ?>"><?= URL::site('/cms/account/reset', Request::current()) ?>?email=<?= $person->emailaddress ?>&token=<?= $token ?></a>.

This link will be valid for one hour.

Thank you.  
