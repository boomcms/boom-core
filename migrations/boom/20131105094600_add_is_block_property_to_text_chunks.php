<?php

class Migration_Boom_20131105094600 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
		$db->query(null, "alter table chunk_texts add is_block boolean default false");
		$db->query(null, "update chunk_texts set is_block = true where slotname like 'bodycopy%'");
	}

	public function down(Kohana_Database $db)
	{
		$db->query(null, "alter table chunk_texts drop is_block");
	}
}