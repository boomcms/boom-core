<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
Dear <?=$person->getName()?>,

As a security precaution, we temporarily freeze access to an account on the CMS if the
password has been entered incorrectly three or more times consecutively.  This is to
prevent unauthorised 'brute force' attempts to access your account.

Your account has just been frozen for this reason.  You can unfreeze it by
resetting your password: 

CMS password reset:   <? URL::base( Request::current() ) ?>/cms/account/forgotten?email=<?=$person->emailaddress?>



