<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20140302172300 extends Minion_Migration_Base
{
	public function up(Kohana_Database $db)
	{
		$db->query(null, "alter table assets change title title varchar(150)");
	}

	public function down(Kohana_Database $db)
	{
	}
}