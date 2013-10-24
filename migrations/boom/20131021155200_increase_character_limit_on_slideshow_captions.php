<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20131021155200 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "alter table chunk_slideshow_slides change caption caption varchar(255)");
	}

	public function down(Kohana_Database $db)
	{
	}
}