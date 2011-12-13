<?php

/**
*
* @package Page
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Page_Cms extends Model_Page {
	private $_can_be_saved = true;
	
	function __construct() {
		parent::__construct( null, true );
	}
	
}


?>
