<?php
/**
* Template used for email to inform a user that their account has been locked.
*
* Rendered by Model_Version_Person::login_failed() when Model_Version_Person::consecutive_failed_login_counter is >= 5.
*
*********************** Variables **********************
*	$person		****	instance of Model_Person	****	The person who's account has been locked.
********************************************************
*
* @uses URL::base()
* @uses Request::current()
*/
?>
Dear <?=$person->name()?>,

As a security precaution, we temporarily freeze access to an account on the CMS if the
password has been entered incorrectly three or more times consecutively.  This is to
prevent unauthorised 'brute force' attempts to access your account.

Your account has just been frozen for this reason.  You can unfreeze it by
resetting your password: 

CMS password reset:   <? URL::base(Request::current()) ?>/cms/account/reset?email=<?=$person->emailaddress?>



