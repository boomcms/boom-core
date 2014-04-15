<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20140319125800 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
		$db->query(null, "alter table chunk_texts add site_text text");
		$db->query(null, "update chunk_texts set site_text = text");

		$db->query(null, 'SET autocommit=0');
		$db->begin();

		foreach (||M::factory('Chunk_Text')->find_all() as $chunk)
		{
			$chunk->update();
		}

		$db->commit();
		$db->query(null, 'SET autocommit=1');
	}

	public function down(Kohana_Database $db)
	{
	}
}