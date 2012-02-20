<?php

/**
* The version table for pages.
*
* Table name: page_v
* 
*************************** Table Columns ************************
****	Name									****	Data Type	****	Description		
****	id										****	integer		****	Primary key. auto increment.			
****	rid										****	integer		****	ID of the page that this version belongs to.
****	template_id								****	integer		****	ID of the template which this page uses.
****	default_child_template_id				****	integer		****	Default template ID for this page's children
****	prompt_for_child_template				****	boolean		****	Doesn't appear to be used at the moment.
****	title									****	string		****	The title of the page.
****	visible_from							****	integer		****	Unix timestamp.
****	visible_to								****	integer		****	Unix timestamp.
****	child_ordering_policy					****	integer		****	How child pages are ordered. This doesn't actually do anything - it's just so that the user can see what the current ordering policy is.
****	children_visible_in_leftnav				****	boolean		****	Should child pages be visible in the leftnav?
****	children_visible_in_leftnav_cms			****	boolean		****	Should child pages be visible in the cms leftnav?
****	approval_process_id						****	integer		****	The approval process to use for edits to this page.
****	ssl_only								****	boolean		****	Should the page require SSL. Not sure this is needed.
****	pagetype_description					****	string		****	Description of the page type. Used in Sledge2 to change the text on the add page button.
****	visible_in_leftnav						****	boolean		****	Whether the page should be shown in the leftnav.
****	visible_in_leftnav_cms					****	boolean		****	Whether the page should be shown in the cms leftnav.
****	keywords								****	string		****	Value for the keywords meta tag.
****	description								****	string		****	Value for the description meta tag.
****	internal_name							****	string		****	Sledge2 used the internal name for certain page lookups. But if you changed the internal name things would break. This seems a bit useless...
****	pagetype_parent_rid						****	integer		****	Nope.
****	children_pagetype_parent_rid			****	integer		****	...
****	default_child_uri_prefix				****	string		****	Default URI prefix for child pages.
****	cache_duration							****	integer		****	Think this was used for the HTTP cache header. Not sure we really need it.
****	indexed									****	boolean		****	Used for the indexed HTML meta tag.
****	sitemap_priority						****	double		****	...
****	sitemap_update_frequency				****	string		****	...
****	default_child_sitemap_priority			****	double		****	...
****	default_child_sitemap__update_frequency	****	string		****	...
****	hidden_from_search_results				****	boolean		****	Determines whether the page appears in search results.
****	default_child_default_child_template_rid****	integer		****	...
****	hidden_from_internal_links				****	boolean		****	Surely if you want to hide it from internal links you should just not link it?
****	search_priority							****	integer		****	Only needed  because Sledge2 search was crap. Could probably be removed.
****	audit_person							****	integer		****	Person ID of the user who created the version.
****	audit_time								****	integer		****	Unix timestamp of when the version was created.
****	deleted									****	boolean		****	Whether the group has been deleted.
****	feature_image							****	integer		****	Asset ID for the image used when featuring this page.
****	enable_rss								****	boolean		****	Whether to enable an RSS version of this page.
******************************************************************
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
	
	protected $_has_many = array(
		'chunks'	=> array( 'through' => 'chunk_page', 'foreign_key' => 'page_vid' ),
	);
	
	/**
	* Filters for the versioned person columns
	* @see http://kohanaframework.org/3.2/guide/orm/filters
	*/
	public function filters()
	{
	    return array(
			'title' => array(
				array( 'html_entity_decode' ),
				array( 'urldecode' ),
			),
	    );
	}
		
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