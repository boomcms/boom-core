<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20130610124400 extends Minion_Migration_Base
{
	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "alter table tags add (slug_short varchar(255) not null, slug_long varchar(255) not null)");
		$db->query(NULL, "create index tags_slug_short on tags(slug_short)");
		$db->query(NULL, "create index tags_slug_long on tags(slug_long)");

		foreach (ORM::factory('Tag')->find_all() as $tag)
		{
			$tag->update();
		}
	}

	public function down(Kohana_Database $db)
	{
		$db->query(NULL, "alter table pages drop primary_uri");
	}
}