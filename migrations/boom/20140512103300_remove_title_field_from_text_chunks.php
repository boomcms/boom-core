<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20140512103300 extends Minion_Migration_Base
{
	public function up(Kohana_Database $db)
	{
		$db->query(null, "alter table chunk_texts drop title");
	}

	public function down(Kohana_Database $db)
	{
	}
}