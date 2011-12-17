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
	protected $_belongs_to = array( 'page' => array( 'model' => 'page', 'foreign_key' => 'active_vid' ) );
	protected $_has_one = array( 
		'template'			=> array( 'model' => 'template', 'foreign_key' => 'id' ),
		'person'			=> array( 'model' => 'person', 'foreign_key' => 'id' ),
		'approval_process'	=> array( 'model' => 'approval_process', 'foreign_key' => 'id' )
	);
	
	/**
	* Holds a reference to the page object to which this version belongs.
	* @access private
	* @var object
	*/
	private $_page;
	
	/**
	* Holds an object representing the parent page.
	* @access private
	* @var object
	*/
	private $_parent;
	
	/**
	* Sets up a reference to the page object which holds this as a version.
	*
	* @param Model_Page $page The page to which we belong.
	*/
	public function setPage( Model_Page $page ) {
		$this->_page =& $page;		
	}
	
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
	* Set the page's title.
	* Method ensures that a new, unique uri is generated from the new new title and stored as a page URI.
	*
	* @uses URI::title()
	* @param string $title
	* @return void
	*/
	public function setTitle( $title ) {
		$this->title = $title;

		if ($this->parent_rid && $this->getParent()->default_child_uri_prefix)
			$prefix = $this->getParent()->default_child_uri_prefix . '/';
		else
			$prefix = ($this->parent_rid) ? $this->getParent()->getPrimaryUri() . '/' : '';

		$append = 0;
		do {
			$uri = URI::title( $title );
			$uri = ($append > 0)? $uri. $append : $uri;
			$uri = strtolower( $uri );

			$append++;
			
			$exists = $this->db->select( '1' )->from( 'page_uri' )->join( 'page_uri_v', 'active_vid', 'id' )->where( 'uri', '=', $uri );
		} while ($exists === 1);
		
		$page_uri = ORM::factory( 'page_uri' );
		$page_uri->version->uri = $uri;
		$this->add( $page_uri );
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
	
	/**
	* Saves the page version object.
	* If the version is new then just does parent::save()
	* If it isn't but it has been changed then we clone the current object, nullify the primary key and then save it to create a new record in the database.
	*
	* @param Validation $validation Validation rules.
	*/
	public function save( Validation $validation = NULL)
	{
		if ($this->loaded() && $this->changed())
		{
			$new_version = clone $this;
			$new_version->$_primary_key = null;
			return $new_version->save();
		} else
			return parent::save();
	}	

}

?>