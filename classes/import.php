<?php

class Import
{	
	public static function chunk_text( $db, $page_vid, $page )
	{
		$chunks = $db->query( Database::SELECT, "select slotname, text from chunk_text inner join chunk_text_v on active_vid = chunk_text_v.id where page_vid = " . $page_vid );
				
		foreach ( $chunks as $chunk )
		{
			$new_chunk = ORM::factory( 'chunk' );
			$new_chunk->type = 'text';
			$new_chunk->slotname = $chunk['slotname'];
			$new_chunk->data->text = $chunk['text'];
			$new_chunk->save();
			
			$page->version->add( 'chunks', $new_chunk );
		}
	}
	
	public static function chunk_feature( $db, $page_vid, $page )
	{
		$chunks = $db->query( Database::SELECT, "select slotname, target_page_rid from chunk_feature inner join chunk_feature_v on active_vid = chunk_feature_v.id where page_vid = " . $page_vid );
				
		foreach ( $chunks as $chunk )
		{
			$new_chunk = ORM::factory( 'chunk' );
			$new_chunk->type = 'feature';
			$new_chunk->slotname = $chunk['slotname'];
			$new_chunk->data->target_page_id = $chunk['target_page_rid'];
			$new_chunk->save();
			
			$page->version->add( 'chunks', $new_chunk );
		}
	}
	
	public static function chunk_asset( $db, $page_vid, $page )
	{
		$chunks = $db->query( Database::SELECT, "select slotname, asset_rid, text from chunk_asset inner join chunk_asset_v on active_vid = chunk_asset_v.id where page_vid = " . $page_vid );
				
		foreach ( $chunks as $chunk )
		{
			$new_chunk = ORM::factory( 'chunk' );
			$new_chunk->type = 'asset';
			$new_chunk->slotname = $chunk['slotname'];
			$new_chunk->data->asset_id = $chunk['asset_rid'];
			$new_chunk->save();
			
			$page->version->add( 'chunks', $new_chunk );
		}
	}
	
	public static function child_pages( $db, $page_rid, $page, $parent )
	{
		$page->reload();
		
		$pages = $db->query( Database::SELECT, "select * from cms_page where parent_rid = " . $page_rid );
		
		foreach( $pages as $p )
		{
			$x = self::import_page( $p );
			
			$mptt = ORM::factory( 'page_mptt' )->values( array( 'page_id' => $p['rid'] ));
			$mptt->insert_as_last_child( $parent );

			// Home page slots.
			Import::chunk_text( $db, $p['vid'], $x );
			Import::chunk_feature( $db, $p['vid'], $x );
			Import::chunk_asset( $db, $p['vid'], $x );

			// Descend down the tree.
			Import::child_pages( $db, $p['rid'], $x, $mptt );
		}		
	}
	
	public static function import_page( array $details )
	{
		$page = ORM::factory( 'page' );
		$page->id = $details['rid'];
		$page->template_id = $details['template_rid'];
		$page->default_child_template_id = $details['default_child_template_rid'];
		$page->prompt_for_child_template = ($details['prompt_for_child_template'] == 't');
		$page->title = $details['title'];
		$page->visible_from = strtotime( $details['visiblefrom_timestamp'] );
		$page->visible_to = strtotime( $details['visibleto_timestamp'] );
		$page->visible =  ($details['ref_page_status_rid'] == 2);
		$page->visible_in_leftnav = ($details['hidden_from_leftnav'] == 'f');
		$page->visible_in_leftnav_cms = ($details['hidden_from_leftnav_cms'] == 'f');
		$page->keywords = $details['keywords'];
		$page->description = $details['description'];
		
		$page->save();

		ORM::factory( 'page_uri' )->values( array( 'page_id' => $details['rid'], 'uri' => $details['uri'], 'primary_uri' => true ))->create();
		
		return $page;
	}
}

?>
