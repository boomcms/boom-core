<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Sledge_20121204110300 extends Minion_Migration_Base
{

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "update chunk_texts set text = replace(text, 'video:', '')");
		$db->query(NULL, "update chunk_texts set text = replace(text, 'youtube.com/embed/', 'youtube.com/watch?v=')");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function down(Kohana_Database $db)
	{
	}
}