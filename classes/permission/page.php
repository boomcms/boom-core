<?php

/**
*
* @package Permissions
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* 
*/
class Permission_Page extends Permission
{
	private $_page;
	
	const VIEW = 1;
	const EDIT = 2;
	const ADD = 4;
	const PCLONE = 8;
	const DELETE = 16;
	
	protected function __construct( Model_Person $person, $page )
	{
		parent::__construct( $person );
		
		$this->_page = $page;
		
		$this->_query->join( 'page_mptt', 'inner' )
					->on( 'permissions.where_id', '=', 'page_mptt.page_id' )
					->where( 'permissions.where_type', '=', 'page' )
					->where( 'page_mptt.lft', '<=', $page->mptt->lft )
					->where( 'page_mptt.rgt', '>=', $page->mptt->rgt )
					->where( 'page_mptt.scope', '=', $page->mptt->scope );
	}
	
	public function can( $action )
	{
		parent::can( $action );
		
		$required;
		
		switch( $action )
		{
			case 'view':
				$required = self::VIEW;
				break;
			case 'edit':
				$required = self::EDIT;
				break;
			case 'add':
				$required = self::ADD;
				break;
			case 'clone':
				$required = self::PCLONE;
				break;
			case 'delete':
				$required = self::DELETE;
				break;
		}
				
		$has = (int) $this->_result->get( 'permission' );
		$perms = (int) $has & $required;
			
		return (bool) $perms;
	}
}

?>