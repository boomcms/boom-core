<?php

/**
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* @todo Work out which methods we actually need from hoopbasepagemodel and implement them nicely. Then just extend ORM 
*
*/
class Model_Page extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'page';
	protected $_has_one = array( 
		'version'	=> array( 'model' => 'version_page', 'foreign_key' => 'id' ),
		'mptt'		=> array( 'model' => 'page_mptt' )
	);
	protected $_has_many = array( 
		'versions'	=> array('model' => 'version_page', 'foreign_key' => 'id'),
		'uris'		=> array('model' => 'page_uri', 'foreign_key' => 'id'),
		'chunks'	=> array('model' => 'chunk', 'through' => 'chunk_page' )
	);
	protected $_belongs_to = array(
		'page'		=> array('foreign_key' => 'id' )
	);
	protected $_load_with = array( 'version' );
	
	/**
	* Page invisible value (stored in page_status column)
	* @var integer
	*/
	const STATUS_INVISIBLE = 1;
	
	/**
	* Page visible value (stored in page_status column)
	* @var integer
	*/
	const STATUS_VISIBLE = 2;
	
	/**
	* Page version status value for a draft version
	* @var integer
	*/
	const STATUS_DRAFT = 1;
	
	/**
	* Page version status value for a version awaiting approval
	* @var integer
	*/
	const STATUS_AWAITING_APPROVAL = 2;
	
	/**
	* Page version status value for an approved version
	* @var integer
	*/
	const STATUS_APPROVED = 3;
	
	/**
	* Page version status value for a published version
	* @var integer
	*/
	const STATUS_PUBLISHED = 4;	
	
	/**
	* Child ordering policy value for manual
	* @var integer
	*/
	const CHILD_ORDER_MANUAL = 1;
	
	/**
	* Child ordering policy value for alphabetic
	* @var integer
	*/
	const CHILD_ORDER_ALPHABETIC = 2;
	
	/**
	* Child ordering policy value for date
	* @var integer
	*/
	const CHILD_ORDER_DATE = 3;
	
	/**
	* @access private
	* @var boolean
	* By default do not allow a page to be saved - overridden by cms_page which is the only time we want to allow saving of pages.
	*/
	private $_can_be_saved = false;
	
	/**
	* @access private
	* @var object
	* Holds the first version of the page. Useful for finding the creation time / person.
	*/
	private $_firstVersion;
	
	/**
	* @access private
	* @var string
	*/
	private $_absolute_uri;
	
	/**
	* Do we need to save the children?
	* Flag indicating whether we've made changes to child pages. Checked by save()
	*
	* @access private
	* @var boolean
	*/
	private $_save_children = false;
	
	/**
	* Holds an array of the slots embedded in the page.
	* Ensures we only query the database once for each slot - and not everytime we want to use the slot.
	* Used by the getSlot() method
	*
	* @access private
	* @var array
	*/
	private $_slots = array();
	
	/**
	* Retrieves a page object using the ORM before checking that it hasn't been deleted. 
	* Pretends that the page wasn't found if it's been deleted.
	* Checks the cache first before using the database.
	*
	* @param int $id Unique key referring to the page in the database. May be a page ID (int).
	* @return page_Model|false Returns an instance of a page if it exists or false if not found (or deleted).
	*
	* @todo Allow passing a URI which joins the page_v table and retrieves by uri
	*/
	function __construct( $key = false, $skip_construct = false ) {
		parent::__construct();
		$cache = Cache::Instance();

		if (is_string( $key )) {
			// If the key is a string assume that it represents a URI - find the page ID associated with that URI.
			$page_id = $cache->get( 'page_id_from_uri_' . md5( $key ) );

			if (!$page_id) {
				// Not in cache? Try the database instead.
				$page_uri = ORM::factory( 'page_uri' )->where( "uri", '=', $key )->find();

				if (!$page_uri->loaded())
					return false;

				$page_id = $page_uri->version->page_id;
				$cache->set( 'page_id_from_uri_' . md5( $key ), $page_id, 'page_uris' );
			}
		} else {
			// Getting a page by ID.
			$page_id = $key;
		}				
		
		$page = $cache->get( 'page_model_by_id_' . $page_id );

		if (!$page) {
			$page = $this->where( 'page.id', '=', $page_id )->find();

			if (is_object($page) && $page->loaded()) {
				$cache->set( 'page_model_by_id_' . $page_id, $this, 'page_objects' );
				$page->version->setPage( $page );
			}
		}

		// If the page is deleted, pretend we didn't find it.
		if ($page->deleted === true)
			return false;
		else
			return $page;	
	}
	
	/**
	* Returns a human readable page status.
	*
	* @return string Page status - currently visible or invisible.
	*/
	public function getPageStatus() {
		switch( $this->page_status ) {
			case self::STATUS_VISIBLE:
				return 'Visible';
				break;
			case self::STATUS_INVISIBLE:
				return 'Invisible';
				break;
			default:
				throw new Kohana_Exception( 'Page has unknown page status value: ' . $this->page_status );
		}		
	}
	
	/**
	* Return a human readable representation of the version status.
	*
	* @return string Version status
	*/
	public function getVersionStatus() {
		switch( $this->version->version_status ) {
			case self::STATUS_DRAFT:
				return 'Draft';
				break;
			case self::STATUS_AWAITING_APPROVAL:
				return 'Awaiting Approval';
				break;
			case self::STATUS_APPROVED:
				return 'Approved';
				break;
			case self::STATUS_PUBLISHED:
				return 'Published';
				break;
			default:
				throw new Kohana_Exception( "Page version has unknown version status: " . $this->version->version_status );
		}	
	}
	
	/**
	* Return a human readable representation of the child ordering policy.
	*
	* @return string Child ordering policy
	*/
	public function getChildOrderingPolicy() {
		switch( $this->child_ordering_policy ) {
			case self::CHILD_ORDER_MANUAL:
				return 'Manual';
				break;
			case self::CHILD_ORDER_ALPHABETIC:
				return 'Alphabetic';
				break;
			case self::CHILD_ORDER_DATE:
				return 'Date';
				break;
			default:
				throw new Kohana_Exception( "Page version has unknown child ordering policy: " . $this->child_ordering_policy );
		}	
	}
	
	/**
	* Checks that the page is visible.
	* @return boolean true if it's visible, false if it isn't
	*/
	public function isVisible() {
		if ($this->page_status === self::STATUS_VISIBLE && $this->visiblefrom() <= time() && ($this->visibleto() >= now() || $this->visibleto() === null))
			return true;
		else
			return false;		
	}
	
	/**
	* Checks that a page is published.
	* @return boolean true if it's published, false if it isn't.
	*/
	public function isPublished() {
		if ($this->version_status === self::STATUS_PUBLISHED)
			return true;
		else
			return false;		
	}
	
	/**
	* Determine whether there is an RSS feed for the page.
	*
	* @return boolean true is there is, false if there isn't
	* @todo Work out how we're actually going to do this in Sledge 3.
	*/
	public function hasRss() {
		return false;
	}
	
    /**
    * Gets an object referring to the first version of the page - used for getting the creation time / author.
    * @return page_v_Model page_v_model object for first version of the page.
    */
	public function getFirstVersion() {
		if ($this->_firstVersion === null) {
			$cache = Cache::Instance();
			$this->_firstVersion = $cache->get( 'page_first_version_for_page_id_' . $this->id );
                    
			if ($this->_firstVersion === null) {
				$this->_firstVersion = ORM::factory( 'page_v')->orderby('audit_time', 'asc')->find_by_rid( $this->id ); 
				$cache->set( 'page_first_version_for_page_id_' . $this->id, $this->_firstVersion, 'page_v_model' );
			}       
		}         
		return $this->_firstVersion;
	}
	
	/**
	* Returns the related mptt_Model object
	* @return object
	*/
	public function getMptt() {
		return $this->mptt;
	}
	
	/**
	* Returns the page's absolute URI.
	* This method uses Kohana's URL::base() method to generate the base URL from the current request (protocol, hostnane etc.) {@link http://kohanaframework.org/3.2/guide/api/URL#base}
	* @uses URL::base()
	* @uses Request::Instance()
	* @todo Direct copy and past from MY_Uri library. Needs to be made properly.
	* @return string The absolute URI.
	*/
	public function getAbsoluteUri() {
		$request = REquest::Instance();
		
		// Get the base URL of the current request.
		$url = URL::base( $request );
		
		$url .= $this->getPrimaryUri();
		
		return $url;		
	}
	
	/**
	* Get the page's primary URI
	* From the page's available URIs finds the one which is marked as the primary URI.
	* @return string The RELATIVE primary URI of the page.
	*
	*/
	public function getPrimaryUri() {
		if ($this->_primary_uri === null)
			$this->_primary_uri = $this->db->select( 'uri' )->from( 'page_uri' )->join( 'page_uri_v', 'active_vid', 'id' )->where( 'page_id', '=', $this->id )->and_where( 'primary', '=', true );
			
		return $this->_primary_uri;		
	}
	
	/**
	* Retrieves a slot belonging to the page, identified by a slotname.
	*
	* @param string $type The type of slot to show.
	* @param string $slotname The name of the slot
	* @param boolean $editable Whether to allow the slot to be editable.
	*
	* @uses slot::factory()
	* @return string The HTML representation of the slot
	*/
	public function getSlot( $type, $slotname, $editable = null)
	{
		if (!in_array( $this->_slots, $slotname ))
		{
			$this->_slots[ $slotname ] = Slot::factory( $type, $this, $slotname, $editable );	
		}
		
		return $this->_slots[ $slotname ];	
	}
	
	/**
	* Set the page's status.
	* Used to set whether the page is visible or invisible. 
	* If a page is being set to invisible then all child pages have their setPageStatus() methods called to ensure that they are also made invisible.
	*
	* @param integer $status Page status value.
	* @return void
	*/
	public function setStatus( $status )
	{
		$this->page_status = $status;
		
		// Are we setting the page to invisble? Hide the children as well, if they're are any.
		if ($status === self::STATUS_INVISIBLE && $this->mptt->countChildren() > 0)
		{	
			foreach ($this->getChildren() as $child)
			{
				$child->setStatus( $status );
			}	
	
			// Set a flag for self::save() to indicate that we've got child pages which need to be saved.
			$this->_save_children = true;		
		}
		
	}
	
	/**
	* Prevents a page from being saved unless it is a cms_page object.
	* The cms_page_Model overrides this function to allow saving.
	*/
	function save( Validation $validation = NULL )
	{
		if (!$this->_can_be_saved)
			throw new Kohana_Exception( "page::save() must be called from an instance of cms_page" );
		
	
		$return =  parent::save();
		
		// Do we need to save our child pages as well?
		if ($this->_save_children === true)
		{
			foreach ($this->getChildren() as $child)
			{
				$child->save();
			}
		}
		
		// Save the new page details to cache.
		$cache = Cache::Instance();
		$cache->set( 'page_model_by_id_' . $this->id, $this );
		
		return $return;
	}
}


?>
