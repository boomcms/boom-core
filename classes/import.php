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
	
	public static function chunk_linkset( $db, $page_vid, $page )
	{
		$chunks = $db->query( Database::SELECT, "select slotname, chunk_linkset.id from chunk_linkset inner join chunk_linkset_v on active_vid = chunk_linkset_v.id where page_vid = " . $page_vid );
				
		foreach ( $chunks as $chunk )
		{
			$new_chunk = ORM::factory( 'chunk' );
			$new_chunk->type = 'linkset';
			$new_chunk->slotname = $chunk['slotname'];
			$new_chunk->save();
			
			$links = $db->query( Database::SELECT, "select name, uri, target_page_rid from linkset_links inner join linkset_links_v on active_vid = linkset_links_v.id where chunk_linkset_rid = " . $chunk['id'] );
			
			foreach( $links as $link )
			{
				$l = ORM::factory( 'linksetlink' );
				$l->chunk_linkset_id = $new_chunk->id;
				$l->title = $link['name'];
				$l->url = $link['uri'];
				$l->target_page_id = $link['target_page_rid'];
				$l->save();
			}
			
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
			
			$mptt = ORM::factory( 'page_mptt' );
			$mptt->id = $p['rid'];
			$mptt->insert_as_last_child( $page_rid );

			// Home page slots.
			Import::chunk_text( $db, $p['vid'], $x );
			Import::chunk_feature( $db, $p['vid'], $x );
			Import::chunk_asset( $db, $p['vid'], $x );
			Import::chunk_linkset( $db, $p['vid'], $x );

			// Descend down the tree.
			Import::child_pages( $db, $p['rid'], $x, $mptt );
		}		
	}
	
	public static function import_page( array $details, $db )
	{
		// Find when the page was created.
		$created = $db->query( Database::SELECT, "select min(audit_time) from page_v where rid = " . $details['rid'] )->as_array();
		
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
		$page->created = strtotime( $created[0]['min'] );
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
		//	$feature = $db->query( Database::SELECT, "select active_vid from asset where id = '" . $feature[0]['item_rid'] . "'" )->as_array();
			$page->feature_image = $feature[0]['item_rid'];
			
			Database::instance()->query( Database::UPDATE, "update page_v set feature_image = '" . $feature[0]['item_rid'] . "' where rid = " . $details['rid'] );
		}
		
		Database::instance()->query( Database::UPDATE, "update page_v set audit_time = '" . strtotime( $details['audit_time'] ) . "' where rid = " . $details['rid'] );

		if ($details['uri'] == '404')
		{
			$details['uri'] = 'error/404';
		}
		
		try
		{
			ORM::factory( 'page_uri' )->values( array( 'page_id' => $details['rid'], 'uri' => $details['uri'], 'primary_uri' => true ))->create();
		}
		catch (Exception $e) {}
		
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
		
		// Import any slideshows on this page.
		$x = $db->query( Database::SELECT, "select target_tag_rid, slotname from chunk_tag inner join chunk_tag_v on active_vid = chunk_tag_v.id where page_vid = " . $details['vid'] )->as_array();
		
		if (sizeof( $x ) > 0)
		{
			foreach ($x as $xx)
			{
				$chunk = ORM::factory( 'chunk' );
				$chunk->type = 'slideshow';
				$chunk->slotname = $xx['slotname'];
				$chunk->save();
	
				$images = $db->query( Database::SELECT, "select item_rid from relationship_partner inner join asset on item_rid = asset.id inner join asset_v on active_vid = asset_v.id where relationship_id in (select relationship_id from relationship_partner where item_tablename = 'tag' and item_rid = " . $xx['target_tag_rid'] . ") and item_tablename = 'asset' and asset.deleted is null order by visiblefrom_timestamp desc" );	
			
				$first = true;
				foreach( $images as $image )
				{
					if (!$page->feature_image && $first == true)
					{
						Database::instance()->query( Database::UPDATE, "update page_v set feature_image = " . $image['item_rid'] . " where rid = " . $page->id );
					}
				
					$s = ORM::factory( 'slideshowimage' );
					$s->chunk_id = $chunk->id;
					$s->asset_id = $image['item_rid'];
					$s->save();
					$first = false;
				}		
			
				$page->version->add( 'chunks', $chunk );
			}
		}
		
		return $page;
	}
	
	public static function child_tags( $db, $parent = null )
	{
		if ($parent == null)
		{
			$tags = $db->query( Database::SELECT, "select tag_v.* from tag inner join tag_v on active_vid = tag_v.id where parent_rid is null" )->as_array();
		}
		else
		{
			$tags = $db->query( Database::SELECT, "select tag_v.* from tag inner join tag_v on active_vid = tag_v.id where parent_rid = " . $parent )->as_array();	
		}
		
		foreach( $tags as $tag )
		{
			$t = ORM::factory( 'tag' );
			$t->id = $tag['rid'];
			$t->name = $tag['name'];
			$t->save();
		
			$mptt = ORM::factory( 'tag_mptt' );
			$mptt->id = $tag['rid'];
			$mptt->insert_as_last_child( $parent );
		
			self::child_tags( $db, $tag['rid'] );	
		}
	}
	
	public static function rss_pages( $db )
	{
		$tags = $db->query( Database::SELECT, "select tag.id from tag inner join tag_v on tag.active_vid = tag_v.id where name = 'Has RSS'" )->as_array();
		$rss_tag = $tags[0]['id'];
		
		// Does the page have RSS enabled?
		$rss_pages = $db->query( Database::SELECT, "select item_rid from relationship_partner where relationship_id in (select relationship_id from relationship_partner where item_tablename = 'tag' and item_rid = " . $rss_tag . ") and item_tablename = 'page'" )->as_array();
		
		foreach( $rss_pages as $p )
		{
			Database::instance()->query( Database::UPDATE, "update page_v set enable_rss = true where rid = " . $p['item_rid'] );
		}
	}
	
	public static function asset_tags( $db, $asset_id )
	{
		$tags = $db->query( Database::SELECT, "select item_rid from relationship_partner where relationship_id in (select relationship_id from relationship_partner where item_tablename = 'asset' and item_rid = " . $asset_id . ") and item_tablename = 'tag'" );
		
		foreach ($tags as $tag)
		{
			$x = ORM::factory( 'tagged_object' );
			$x->object_type = Model_Tagged_Object::OBJECT_TYPE_ASSET;
			$x->object_id = $asset_id;
			$x->tag_id = $tag['item_rid'];
			
			try
			{
				$x->save();
			}
			catch (Exception $e){}
		}
	}
}

?>
