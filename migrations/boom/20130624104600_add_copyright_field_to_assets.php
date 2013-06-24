<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20130613123700 extends Minion_Migration_Base
{
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "alter table assets add copyright varchar(255)");
	}

	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "alter table assets drop copyright");
	}
}
