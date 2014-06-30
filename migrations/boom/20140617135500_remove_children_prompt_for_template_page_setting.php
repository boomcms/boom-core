<?php

class Migration_Boom_20140617135500 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
		$db->query(null, "alter table pages drop children_prompt_for_template");
	}

	public function down(Kohana_Database $db)
	{
	}
}