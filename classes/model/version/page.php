<?php

/**
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Version_Page extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'page_v';
	protected $_belongs_to = array( 
		'person'			=> array( 'model' => 'person', 'foreign_key' => 'id' ),
		'approval_process'	=> array( 'model' => 'approval_process', 'foreign_key' => 'id' )
	);
	
	protected $_has_one = array(
		'page'				=> array( 'model' => 'page', 'foreign_key' => 'id' ),
		'template'			=> array( 'model' => 'template', 'foreign_key' => 'id' ),
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
	public function getDescription() {
		$description = ($this->description)? $this->description : 'Page description';
		
		return $description;		
	}
	
	/**
	* Get the visiblefrom_timsetamp property
	* Turns the timestmap into a human readable time.
	*
	* @return string Visible from time.
	*/
	public function getVisibleFrom() {
		return $this->visiblefrom_timestamp;
	}
	
	/**
	* Get the visibleto_timestamp property
	* Turns the timestmap into a human readable time.
	*
	* @return string Visible to time.
	*/
	public function getVisibleTo() {
		return $this->visibleto_timestamp;
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
	
	/**
	* Set the page's parent. 
	* Set's the current versions parent_id property but also configures some MPTT type stuff.
	*
	* @param Model_Page A page object representing the parent page.
	* @return void
	*/
	public function setParent( Model_Page $parent ) {
		$this->parent_id = $parent->id;
		
		$this->_parent->mptt->left_val = $parent->mptt->left_vall + 1;
	}
	
	/**
	* Returns an object representing the parent page.
	* @return false|page_Model Parent page.
	*/
	public function getParent() {
		if ($this->_parent === null) {
			if ($this->parent_rid === null)
				$this->_parent = false;
			else
				$this->_parent = new static( $this->parent_rid );
				
			if (!$this->_parent instanceof page_Model) //Something has gone amiss.
				throw new Kohana_Exception( 'Our parent page isn\'t a page' );
		}
	
		return $this->_parent;		
	}
	
	/**
	* Set the child ordering policy.
	* Sets the property and does associated stuff like calling page_mptt::setOrder().
	*
	* @uses Model_Page_Mptt::setOrderBy()
	* @param integer $policy The child ordering policy ID
	* @return void
	*/
	public function setChildOrderingPolicy( $policy ) {
		$this->child_ordering_policy = $policy;
		
		// Reorder the MPTT tree.
		$this->_page->mptt->setOrderBy( $policy );		
	}
}

?>