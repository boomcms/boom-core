<?php
/**
* Database config creation template.
* @package Templates
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
?>

Please enter your database connection information:
<br />
	
<form action='/setup/config' method='post'>
	<input type='hidden' name='group' value='database' />
	Hostname: <input type='text' name='hostname' value='localhost' />
	Username: <input type='text' name='username' />
	Password: <input type='password' name='password' />
	Database Name: <input type='text' name='dbname' />
	<input type='submit' />
</form>