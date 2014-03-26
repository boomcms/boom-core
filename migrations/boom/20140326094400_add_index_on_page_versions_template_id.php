<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20140326094400 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "create index page_versions_template_id on page_versions(template_id)");
	}

	public function down(Kohana_Database $db)
	{
	}
}