<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20130624104600 extends Minion_Migration_Base
{
	public function up(Kohana_Database $db)
	{
		$db->query(null, "alter table assets add copyright varchar(255)");
	}

	public function down(Kohana_Database $db)
	{
		$db->query(null, "alter table assets drop copyright");
	}
}
