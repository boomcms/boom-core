<?php

/**
*
* Table name: page
* This table is versioned!
*
*************************** Table Columns ************************
****	Name			****	Data Type	****	Description					
****	id				****	integer		****	Primary key, auto increment
****	active_vid		****	string		****	ID of the current version.
****	sequence		****	string		****	Not sure, think it's used for manually ordering pages.
****	published_vid	****	integer		****	The ID of the published version.
****	visible			****	boolean		****	Whether the page is visible or invisible.
******************************************************************
*
* @see Model_Version_Page
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Page extends ORM_Versioned {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'page';
	protected $_has_one = array( 
		'mptt'		=> array( 'model' => 'page_mptt' ),
		'feature_image' => array( 'model' => 'asset', 'foreign_key' => 'id' ),
	);
	protected $_has_many = array( 
		'uris'		=> array('model' => 'page_uri', 'foreign_key' => 'page_id'),
		'revisions' => array('model' => 'version_page', 'foreign_key' => 'rid' ),
	);
	protected $_belongs_to = array(
	//	'published_version'  => array( 'model' => 'version_page', 'foreign_key' => 'published_vid' ), 
		'version'  => array( 'model' => 'version_page', 'foreign_key' => 'published_vid' ), 
	);
	protected $_load_with = array( 'version' );
	
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
	const CHILD_ORDER_DATE = 4;
	
	/**
	* Child ordering policy for ascending.
	* @var integer
	*/
	const CHILD_ORDER_ASC = 8;

	/**
	* Child ordering policy for descending.
	* @var integer
	*/
	const CHILD_ORDER_DESC = 16;	
	
	/**
	* Holds the calculated primary URI
	*
	* @access private
	* @var string
	*/
	private $_primary_uri;
	
	/**
	* Cached result for self::url()
	*
	* @access private
	* @var string
	*/
	private $_url;
	
	/**
	* Holds an array of the slots embedded in the page.
	* Ensures we only query the database once for each slot - and not every time we want to use the slot.
	* Used by the get_slot() method
	*
	* @access private
	* @var array
	*/
	private $_slots = array();
	
	/**
	* Load database values into the object.
	*
	* This is customised to ensure that a user who cannot edit the current page sees the current, published version.
	* While someone who can edit the page sees the current version, whatever it's status.
	* Essentially this function replaces $this->version if the user can edit the page.
	* This isn't a very efficient way of doing it since it means that the published version is loaded and then the published version
	* Resulting in two database queries when we only need the data from one.
	* However, I can't see any other way of doing this at the moment, within the constraints of Kohana.
	*
	* This logic is here, rather than __construct or _initialize as putting this code in those methods wouldn't work for loading pages through related objects, e.g. $page_uri->page.
	*/
	protected function _load_values(array $values)
	{		
		parent::_load_values( $values );
		
		if ($this->loaded())
		{
			$person = Auth::instance()->get_user();

			if ($person->can( 'edit', $this ) && $this->active_vid != $this->published_vid)
			{
				$this->version->clear();
				$this->version->where( 'id', '=', $this->active_vid )->find();
			}
		}
	}
	
	/**
	* Adds a new child page to this page's MPTT tree.
	* Takes care of putting the child in the correct position according to this page's child ordering policy.
	*
	* @param Model_Page $page The new child page.
	* @return void
	*/
	public function add_child( Model_Page $page )
	{
		if ($this->child_ordering_policy & self::CHILD_ORDER_DATE)
		{
			if ($this->child_ordering_policy & self::CHILD_ORDER_ASC)
			{
				$page->mptt->insert_as_last_child( $this->mptt );
			}
			else
			{
				$page->mptt->insert_as_first_child( $this->mptt );
			}
		}
		else if ($this->child_ordering_policy & self::CHILD_ORDER_ALPHABETIC)
		{
			// Ordering alphabetically? 
			// Find the page_mptt record of the page which comes after this alphabetically.
			$mptt = ORM::factory( 'page_mptt' )
					->join( 'page', 'inner' )
					->on( 'page.id', '=', 'page_mptt.page_id' )
					->join( 'page_v', 'inner' )
					->on( 'page.active_vid', '=', 'page_v.id' )
					->where( 'title', '>', $page->title );
					
			if ($this->child_ordering_policy & self::CHILD_ORDER_ASC)
			{
				$mptt->order_by( 'title', 'asc' );
			}
			else
			{
				$mptt->order_by( 'title', 'desc' );
			}
			
			$mptt->limit( 1 )->find();
						
			if (!$mptt->loaded())
			{
				// If a record wasn't loaded then there's no page after this one.
				// Insert as the last child of the parent.
				$page->mptt->insert_as_last_child( $this->mptt );
			}
			else
			{
				$page->mptt->insert_as_prev_sibling( $mptt );
			}
		}
		else
		{
			// For anything else (such as ordering children manually) just stick it at the end for now.
			$page->mptt->insert_as_last_child( $this->mptt );
		}		
	}
	
	/**
	* Return a human readable representation of the version status.
	*
	* @return string Version status
	*/
	public function getVersionStatus() {
		switch( $this->version_status ) {
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
				return null;
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
				throw new Sledge_Exception( "Page version has unknown child ordering policy: " . $this->child_ordering_policy );
		}	
	}
	
	public function is_visible()
	{
		$time = time();
		
		return ($this->visible && $this->visible_from <= $time && ($this->visible_to >= $time || $this->visible_to == 0));
	}
	
	/**
	* Checks that a page is published.
	* @return boolean true if it's published, false if it isn't.
	*/
	public function is_published() {
		return $this->published_vid == $this->version->id;
	}
	
	/**
	* Determine whether a published version exists for the page.
	*
	* @return bool
	*/
	public function has_published_version()
	{
		return !($this->published_vid == 0);
	}
	
	/**
	* Delete a page.
	* Ensures child pages are deleted and that the pages are deleted from the MPTT tree.
	*
	* @return ORM
	*/
	public function delete()
	{
		foreach( $this->mptt->children() as $p )
		{
			$p->page->delete();
		}
		
		$this->mptt->delete();
		return parent::delete();
	}
	
	/**
	* Returns the page's absolute URI.
	* This method uses Kohana's URL::base() method to generate the base URL from the current request (protocol, hostnane etc.) {@link http://kohanaframework.org/3.2/guide/api/URL#base}
	* @uses URL::base()
	* @uses Request::Instance()
	* @todo Direct copy and past from MY_Uri library. Needs to be made properly.
	* @return string The absolute URI.
	*/
	public function url() {
		if ($this->_url === null)
		{
			// Get the base URL of the current request.
			$this->_url = URL::base( Request::current() );
		
			if ($this->_url != '')
			{
				$this->_url .= $this->get_primary_uri();
			}
		}
		
		return $this->_url;		
	}
	
	/**
	* Get the page's primary URI
	* From the page's available URIs finds the one which is marked as the primary URI.
	* @return string The RELATIVE primary URI of the page.
	*
	*/
	public function get_primary_uri() {
		if ($this->_primary_uri === null)
		{
			$uri = DB::select( 'uri' )
			->from( 'page_uri' )
			->where( 'page_id', '=', $this->id )
			->and_where( 'primary_uri', '=', true )
			->execute();
			
			$this->_primary_uri = $uri->get( 'uri' );
		}
			
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
	public function get_slot( $type, $slotname, $editable = null)
	{
		if (!array_key_exists( $slotname, $this->_slots ))
		{
			$this->_slots[ $slotname ] = ORM::factory( "chunk" )
											->join( 'chunk_page' )
											->on( 'chunk_page.chunk_id', '=', 'chunk.id' )
											->where_open()
											->where( 'chunk_page.page_vid', '=', $this->version->id )	
											->or_where( 'chunk_page.page_vid', '=', 0 )
											->where_close()										
											->where( 'slotname', '=', $slotname )
											->order_by( 'page_vid', 'desc' )
											->find();
		}
		
		return $this->_slots[ $slotname ];	
	}
	
	/**
	* Used to re-order this page's children
	*
	* @param int $order The ordering policy ID.
	* @param string $direction The order direction, should be 'asc' or 'desc'
	* @todo lock mptt tables to avoid errors.
	*/
	public function order_children( $order, $direction )
	{
		$direction = ($direction == 'asc')? 'asc' : 'desc';
		
		if ($order !== self::CHILD_ORDER_MANUAL && $this->mptt->has_children())
		{
			// Find the children, sorting the database results by the column we want the children ordered by.
			$query = ORM::factory( 'page_mptt' )
				->join( 'page', 'inner' )->on( 'page_mptt.page_id', '=', 'page.id' )
				->join( 'page_v', 'inner' )->on( 'page.active_vid', '=', 'page_v.id' )
				->where( 'parent_id', '=', $this->mptt->id );
				
			if ($order == self::CHILD_ORDER_ALPHABETIC)
				$query->order_by( 'title', $direction );
			else
				$query->order_by( 'audit_time', $direction );
			
			$children = $query->find_all();
			
			$previous = null;
			
			// Loop through the children assigning new left and right values.
			foreach( $children as $child )
			{
				if ($previous === null)
				{
					$child->move_to_first_child( $this->mptt );
					$first = false;
				}
				else
				{
					$child->move_to_next_sibling( $previous );
				}
				
				$previous = $child;
			}
		}	
		
		$direction = ($direction == 'asc')? self::CHILD_ORDER_ASC : self::CHILD_ORDER_DESC;	
		$this->child_ordering_policy = $direction | $order;
	}
	
	/**
	* Generates a unique URI for the page based on the title.
	*
	* @return string The new primary URI
	*/
	public function generate_uri()
	{
		$parent = $this->mptt->parent()->page;
	
		if ($parent->default_child_uri_prefix)
			$prefix = $parent->default_child_uri_prefix . '/';
		else
			$prefix = $parent->get_primary_uri() . '/';

		$append = 0;
		$start_uri = $prefix . URL::title( strtolower( $this->title ) );
	
		// If we're adding a page to the root node the URL ends up starting with a '/', which we don't want.
		// So check for and remove a '/' from the start of the URI.
		if ($start_uri[0] == '/')
			$start_uri = substr( $start_uri, 1 );
		
		// Get a unique URI.
		do {
			$uri = ($append > 0)? $start_uri. $append : $start_uri;
			$append++;
		
			$exists = (int) DB::select( 'page_uri.id' )
						->from( 'page_uri' )
						->where( 'uri', '=', $uri )
						->limit( 1 )
						->execute()
						->get( 'id' );
		} while ($exists !== 0);
	
		// Create a URI for the page.
		$page_uri = ORM::factory( 'page_uri' )
					->values( 
						array( 'uri' => $uri, 'primary_uri' => true ) 
					)
					->create();
					
		$this->add( $page_uri );	
		
		$this->_primary_uri = $uri;	
		return $uri;
	}
}


?>
