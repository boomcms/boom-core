<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20130905114000 extends Minion_Migration_Base
{
	public function up(Kohana_Database $db)
	{
		$db->query(null, "alter table assets change copyright credits varchar(255)");
	}

	public function down(Kohana_Database $db)
	{
		$db->query(null, "alter table assets change credits copyright varchar(255)");
	}
}
