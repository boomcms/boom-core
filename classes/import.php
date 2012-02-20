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
			$x = self::import_page( $p, $db );
			
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
	
	public static function import_page( array $details, $db )
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
		$page->visible_in_leftnav = (bool) !($details['hidden_from_leftnav'] == 't');
		$page->visible_in_leftnav_cms = (bool) !($details['hidden_from_leftnav_cms'] == 't');
		$page->keywords = $details['keywords'];
		$page->description = $details['description'];
		$page->internal_name = $details['internal_name'];
		$page->save();
		
		// Import secondary URIs.
		$uris = $db->query( Database::SELECT, "select uri from secondary_uri inner join secondary_uri_v on active_vid = secondary_uri_v.id where page_rid = " . $details['rid'] );
		
		foreach( $uris as $uri )
		{
			$page_uri = ORM::factory( 'page_uri' );
			$page_uri->page_id = $page->id;
			$page_uri->uri = $uri['uri'];
			$page_uri->primary_uri = false;
			
			try
			{
				$page_uri->save();
			}
			catch( Sledge_Exception $e ){}			
		}
		
		// Find the page's feature image.
		$feature = $db->query( Database::SELECT, "select item_rid from relationship_partner where item_tablename = 'asset' and relationship_id = (select relationship_id from relationship_partner where description = 'featureimage' and item_tablename = 'page' and item_rid = " . $details['rid'] . ")" )->as_array();
		
		if (sizeof( $feature ) > 0)
		{
			$feature = $db->query( Database::SELECT, "select active_vid from asset where id = '" . $feature[0]['item_rid'] . "'" )->as_array();
			$page->feature_image = $feature[0]['active_vid'];
			
			Database::instance()->query( Database::UPDATE, "update page_v set feature_image = '" . $feature[0]['active_vid'] . "' where rid = " . $details['rid'] );
		}
		
		Database::instance()->query( Database::UPDATE, "update page_v set audit_time = '" . strtotime( $details['audit_time'] ) . "' where rid = " . $details['rid'] );

		ORM::factory( 'page_uri' )->values( array( 'page_id' => $details['rid'], 'uri' => $details['uri'], 'primary_uri' => true ))->create();
		
		// Import tags for this page.
		$tags = $db->query( Database::SELECT, "select item_rid from relationship_partner inner join (select relationship_id from relationship_partner where item_tablename = 'page' and item_rid = " . $details['rid'] . ") as q on q.relationship_id = relationship_partner.relationship_id where item_tablename = 'tag'" );
		
		foreach( $tags as $tag )
		{
			$to = ORM::factory( 'tagged_object' );
			$to->tag_id = $tag;
			$to->object_type = Model_Tagged_Object::OBJECT_TYPE_PAGE;
			$to->object_id = $details['rid'];
			
			try
			{
				$to->save();
			}
			catch( Database_Exception $e ){}
		}
		
		return $page;
	}
	
	public static function child_tags( $db, $parent = 1 )
	{
		$tags = $db->query( Database::SELECT, "select tag_v.* from tag inner join tag_v on active_vid = tag_v.id where parent_rid = " . $parent )->as_array();
		
		foreach( $tags as $tag )
		{
			if ($parent != 1 || $tag['name'] == 'Pages')
			{
				$t = ORM::factory( 'tag' );
				$t->id = $tag['rid'];
				$t->name = $tag['name'];
				$t->save();
			
				$mptt = ORM::factory( 'tag_mptt' );
				$mptt->id = $tag['rid'];
			
				if ($parent == 1)
				{
					$mptt->make_root();
				}
				else
				{
					$mptt->insert_as_last_child( $parent );
				}
			
				self::child_tags( $db, $tag['rid'] );	
			}
		}
	}
}

?>
