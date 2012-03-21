<?php
/**
* Database creation template.
* This is displayed to aid setting up the database of a new Sledge site.
* At the moment datbase setup only uses a gited SQL dump but at some point this template should give more options (such as copying an existing database).
*
* Rendered by Sledge_Controller::__construct();
*
*********************** Variables **********************
*	$dbname			****	string		****	The name of the database this Sledge site is expecting to use. Taken from the datbase config file.
********************************************************
*
* @package Templates
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
?>
	
The database <?=$dbname?> does not exist. Wanna try and create it?
	
<a href='/?creatdb'>Hell yeah!</a>
