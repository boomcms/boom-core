<?php

class Migration_Boom_20140210105800 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
		$db->query(null, "alter table chunk_texts change title title varchar(200)");
	}

	public function down(Kohana_Database $db)
	{
	}
}