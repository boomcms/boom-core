<?php

class Migration_Boom_20141118154400 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
                $db->query(null, "alter table chunk_assets change caption caption text");
        }

	public function down(Kohana_Database $db)
	{
	}
}