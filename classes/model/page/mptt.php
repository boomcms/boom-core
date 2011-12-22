<?php

/**
* This model handles page MPTT records and associated methods. We also have an MPTT library. The library has functions which relate to the MPTT tree as a whole - this model should be used in relation to a particular record in the MPTT tree.
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* @link http://www.sitepoint.com/hierarchical-data-database-2/
* @todo MPTT trees currently don't support child order policies.
*
*/
class Model_Page_Mptt extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'page_mptt';
	protected $_belongs_to = array( 'page' => array( 'foreign_key' => 'page_id' ) );
	protected $_has_one = array( 'page' => array( 'foreign_key' => 'id' ) );
	
	/**
	* Store the old left value when it's changed so that we can close the gap in the tree if we move this page elsewhere.
	* @access private
	* @var integer
	*/
	private $_old_left;
	
	/**
	* Holds the calculated ancestors array to prevent calculating it more than once.
	* @access private
	* @var array
	*/
	private $_ancestors;
	
	/**
	* Holds the calculated descendants array to prevent calculating it more than once.
	* @access private
	* @var array
	*/
	private $_children;
	
	/**
	* Hold the new child ordering policy.
	* Property is only set when we set a new child ordering policy. Used when save() is called to determine how to order the children.
	* @access private
	* @var string
	*/
	private $_ordering_policy;
	
	/**
	* Stores the result of self::getRoute()
	* @access private
	* @var array
	*/
	private $_route;
	
	/**
	* Set the current page's MPTT left value. Method will automatically set a right value of left+1 (It's assumed for now that we're creating a new page and won't therefore have any children).
	*
	* @param integer $val The left value.
	* @return void
	*/
	public function setLeftVal( $val ) {
		if (!is_integer( $val ))
			throw new Kohana_Exception( 'Cannot set MPTT left to a non-numeric value' );
			
		$this->_old_left = $this->left_val;
			
		$this->left_val = $val;
		$this->right_val = $val + 1;
	}
	
	/**
	* Get all pages prior to this one in the tree. Returns an array of MPTT objects which may be used to access the page objects.
	* The ancestor which is farthest in the tree is the first element in the array. This page's parent is the last element.
	*
	* @return array Array of ancestors.
	*/
	public function getAncestors() {
		if ($this->_ancestors === null) {
			$this->_ancestors = ORM::factory( 'page_mptt' )->where( 'left_val', '< ', $this->left_val )->and_where(  'right_val', '>', $this->right_val )->order_by( 'left_val' )->find_all()->as_array();
		}
		
		return $this->_ancestors;		
	}

	/**
	* Get all pages below this one in the tree. Returns an array of MPTT objects which may be used to access the page objects.
	* It may be somewhat confusing that we use getAncestors() for parents and getChildren() for descendants but descendants risks code errors creeping in from difficult to spot spelling mistakes.
	*
	* @return array Array of children.
	*/	
	public function getChildren() {
		if ($this->_children === null) {
			$this->_children = ORM::factory( 'page_mptt' )->where( 'left_val',  'between', array( $this->left_val, $this->right_val ) )->order_by( 'left_val' )->find_all()->as_array();
		}
		
		return $this->_children;		
	}
	
	public function getTree() {
		return array_merge( $this->getAncestors(), $this->getChildren() );
	}
	
	/**
	* The determines the route to get to the current page.
	* This is used for generating the top nav.
	*
	* @return array Array of mptt objects.
	*/
	public function getRoute()
	{
		if ($this->_route === null)
		{
			$this->_route = ORM::factory( 'page_mptt' )->where( 'left_val', '<=', $this->left_val )->and_where(  'right_val', '>=', $this->right_val )->order_by( 'left_val' )->find_all()->as_array();
		}		
		
		return $this->_route;
	}
	
	/**
	* Returns the number of children the current page has.
	*
	* @return integer Kiddy count
	*/
	public function countChildren() {
		return (($this->right_val - $this->left_val) - 1) / 2;
	}
	
	/**
	* Set the child ordering policy.
	*
	* @param integer $policy The child ordering policy ID.
	* @return void
	*/
	protected function setOrderBy( $policy ) {
		$this->_ordering_policy = $policy;
	}
	
	/**
	* Called when a page is saved to save the page's MPTT values. Ensures that the tree remains intact.
	*
	* @return void
	* @todo Use  $this->_ordering_policy to order the children by a child ordering policy
	*/
	public function save( Validation $validation = NULL ) {
		// Check that something's actually been changed. If not just return without throwing an error, there's no need to cause a fuss.
		if (count( $this->changed ) === 0)
			return true;
		
		if ($this->loaded) {
			// Updating an existing MPTT record.
			
			// If the page is moving close the gap in the tree where we used to be.
			if ($this->_old_left !== null) {
				$this->db->query( "update page_mptt set right_val = right_val - 2 where right_val > " . $this->_old_left );
				$this->db->query( "update page_mptt set left_val = left_val - 2 where left_val > " . $this->_old_left );
			}		
		}
		
		// Make space in the tree for us.
		$this->db->query( "update page_mptt set right_val = right_val + 2 where right_val > " . $this->left_val );
		$this->db->query( "update page_mptt set left_val = left_val + 2 where left_val > " . $this->left_val );
		
		// ORM can do the save for us. Why should we do everything?
		parent::save();
	}
}


?>