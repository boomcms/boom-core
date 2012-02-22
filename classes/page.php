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
}

?>
