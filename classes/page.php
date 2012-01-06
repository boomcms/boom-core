<?php defined('SYSPATH') or die('No direct script access.');

/**
* Page class.
* Provides command methods to the page interfaces.
* @package Page
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
abstract class Page 
{
	/**
	* Holds the page which we're interfacing
	*
	* @access private
	* @var object
	*/
	protected $_page;
	
	/**
	* Holds an array of the slots embedded in the page.
	* Ensures we only query the database once for each slot - and not everytime we want to use the slot.
	* Used by the get_slot() method
	*
	* @access private
	* @var array
	*/
	private $_slots = array();
	
	/**
	* Page constructor
	* Gets the page from the database and stores it in the $_page property.
	*
	* @param int $page_id
	* @return void
	*/
	public function __construct( $page )
	{
		if (is_object( $page ))
			$this->_page = $page;
		else
			$this->_page = ORM::factory( 'page', $page_id );
	}
	
	/**
	* Factory method for retrieving a page
	* 
	* @param $type string The type of page. Can be cms or site
	* @param $page_id int The ID of the page
	* @return Page The page object
	*/
	public static function factory( $type, $page_id )
	{
		switch( $type )
		{
			case 'cms':
				return new Page_Cms( $page_id );
			default:
				return new Page_Site( $page_id );
		}		
	}
	
	public function __get( $property )
	{
		return $this->_page->$property;
	}
	
	public function __set( $property, $value )
	{
		return $page->_page->$property = $value;
	}
	
	public function __isset( $property )
	{
		return isset( $this->_page, $property );
	}
	
	/**
	* Pass any method calls not handled by this class to the page object.
	*
	* @param string $method Method name
	* @param array $args array of arguments
	*/
	public function __call( $method, $args )
	{
		try
		{
			$method = new ReflectionMethod( get_class( $this->_page ), $method );
			return $method->invokeArgs( $this->_page, $args );
		}
		catch (ReflectionException $c)
		{
			$method = new ReflectionMethod( get_class( $this->_page->version ), $method );
			return $method->invokeArgs( $this->_page->version, $args );
		}
	}	
	
	public function __toString()
	{
		return $this->_page->__toString();
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
			$this->_slots[ $slotname ] = ORM::factory( "chunk_$type" )
											->with( "chunk" )
											->on( 'chunk.active_vid', '=', "chunk_$type" . ".id" )
											->join( 'chunk_page' )
											->on( 'chunk_page.chunk_id', '=', 'chunk.id' )
											->where( 'chunk_page.page_id', '=', $this->_page->id )											
											->where( 'slotname', '=', $slotname )
											->find();
		}
		
		return $this->_slots[ $slotname ];	
	}
	
	/**
	* Find which pages to display in this page's lefnav
	*
	* @uses Model_Person::logged_in()
	* @return ORM_Iterator Pages to display in leftnav
	*/
	public function leftnav_pages( Model_Person $person )
	{	
		$query = ORM::factory( 'page' )
					->join( 'page_mptt' )
					->on( 'page_mptt.page_id', '=', 'page.id' )
					->where( 'scope', '=', $this->_page->mptt->scope )
					->where( 'page_v.deleted', '=', 'f' );	
					
					
		// CMS or Site leftnav?
		if (!$person->logged_in())
		{
			$query->where( 'page_v.visible_in_leftnav', '=', 't' )
				  ->where( 'page.page_status', '=', Model_Page::STATUS_VISIBLE );	
		}
		else
		{	
			$query->where( 'page_v.visible_in_leftnav_cms', '=', 't' );
		}
		
		$query->order_by( 'page_mptt.lft', 'asc' );
		$pages = $query->find_all();
		
		return $pages;
	}
}

?>