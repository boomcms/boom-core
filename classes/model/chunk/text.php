<?php
/**
* Model for the text chunk table.
*
* Table name: chunk_text
*
*************************** Table Columns ************************
****	Name			****	Data Type	****	Description					
****	id				****	integer		****	Primary key, auto increment
****	text			****	string		****	The content of the text chunk.
****	audit_person	****	integer		****	ID of the person who edited the chunk.
****	audit_time		****	integer		****	Unix timestamp of when the chunk was edited.
****	deleted			****	boolean		****	Whether the chunk has been deleted.
******************************************************************
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Chunk_Text extends ORM implements Interface_Slot
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'chunk_text';
	protected $_primary_key = 'chunk_id';
	
	/**
	* Filters for the versioned person columns
	* @see http://kohanaframework.org/3.2/guide/orm/filters
	*/
	public function filters()
	{
	    return array(
			'text' => array(
				array( 'html_entity_decode' ),
				array( 'urldecode' ),
			),
	    );
	}
	
	public function show( $template = null)
	{
		if ($this->loaded())
		{
			// Fix image links.
			$text = $this->text;
			$text = preg_replace( "|hoopdb://image/(\d+)|", "/asset/view/$1/0/400", $text );
			
			// Fix internal page links.
			$text = preg_replace_callback( "|hoopdb://page/(\d+)|", 
				function ($match)
				{
					$p = ORM::factory( 'page', $match[1] );
					return $p->url();
				}, 
				$text 
			);
			
			// Utoob video links in the form video:blahblahblah
			// Setting default height and width needs to be improved - we don't want to have to do it for every text slot.
			$video_width = Kohana::$config->load( 'config' )->get('video_width');
			$video_width = ($video_width != '')? $width_width : '560';
			
			$video_height = Kohana::$config->load( 'config' )->get('video_height');
			$video_height = ($video_height != '')? $width_height : '315';

			$text = preg_replace( "|video:([a-zA-Z0-9:/._\-]+)|", "<iframe width='$video_width' height='$video_height' src='$1' frameborder='0' class='video' allowfullscreen></iframe>", $text );
			
			if ($template == 'standfirst')
			{
				$v = View::factory( "site/slots/text/$template" );
				$v->text = $text;
				return $v;
			}
			else
			{
				return $text;
			}
		}
		else
		{
			return '';
		}
	}
	
	public function show_default()
	{
		return 'Click on me to add some text here.';
	}
	
	public function get_slotname()
	{
		return $this->chunk->slotname;
	}
	
	public function getTitle()
	{
		
		
		
	}
	
	public function __toString()
	{
		return $this->show();
	}
	
}

?>
