<?php defined('SYSPATH') or die('No direct script access.');

/**
* iWorkflow interface
* Ensures that all objects which may be workflowed have methods for retrieving their published status etc.
* @package Workflow
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
interface iWorkflow
{

	public function getStatus();
	
}

?>
