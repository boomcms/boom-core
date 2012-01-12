<?
/**
* Email template for CMS password reset emails.
*/
?>

Dear <?=$person->getName()?>,

You have requested a new password for <?= URL::base( Request::current() ) ?>cms/login

Your new password is: <?=$password ?>


Please keep this new password in a secure location.

Login to the CMS at <?= URL::base( Request::current() ) ?>cms/login?email=<?=$person->emailaddress?>

Thank you.  
