<?php

/**
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Version_Page extends Model_Version {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'page_v';
	protected $_belongs_to = array( 
		'template'			=> array( 'model' => 'template', 'foreign_key' => 'template_id' ),
		'approval_process'	=> array( 'model' => 'approval_process', 'foreign_key' => 'id' ),
		'person'	=> array( 'model' => 'person', 'foreign_key' => 'audit_person' ),
		'image'	=>	array( 'model'	=> 'asset', 'foreign_key' => 'feature_image' ),
	);
	
	protected $_has_one = array(
		'page'		=> array( 'model' => 'page', 'foreign_key' => 'id' ),
	);
	
	/**
	* Holds an object representing the parent page.
	* @access private
	* @var object
	*/
	private $_parent;
		
	/**
	* Get the page description.
	* Returns $this->description if set or the current page's standfirst if not.
	*
	* @return string The page description.
	* @todo Retrieval of 'standfirst' text chunk.
	*/
	public function get_description() {
		return $this->description;		
	}
	
	public function get_keywords() {
		return $this->keywords;		
	}
	
	/**
	* Does the page have a feature image set?
	*
	* @return bool
	*/
	public function has_image()
	{
		if ($this->feature_image == 0 || !$this->image->loaded())
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	/**
	* Get the page type description.
	* Mostly seems to be used for the 'add page' link in the CMS bar.
	* For most pages this will return 'page' but for special cases where the page represents something a different string is returned.
	* For example with NHHG properties where we want the link to say 'Add Property'.
	*
	* @return string The page type description
	*/
	public function getPageTypeDescription()
	{
		if ($this->pagetype_description != '')
		{
			return $this->pagetype_description;
		}
		else 
		{
			return 'Page';
		}
	}
}

?>