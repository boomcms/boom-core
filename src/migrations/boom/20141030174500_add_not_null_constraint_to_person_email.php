<?php

class Migration_Boom_20141030174500 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
		$db->query(null, "delete from people where email is null");
		$db->query(null, "alter table people change email email varchar(255) not null");
	}

	public function down(Kohana_Database $db)
	{
	}
}