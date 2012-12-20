<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Initial boom core structure.
 */
class Migration_Boom_20121120105700 extends Minion_Migration_Base
{

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "alter table asset_versions add uploaded_by mediumint unsigned, add uploaded_time int unsigned, add last_modified int unsigned");
		$db->query(NULL, "update asset_versions set last_modified = audit_time");
		$db->query(NULL, "update asset_versions av1 inner join asset_versions av2 on av1.rid = av2.rid inner join (select min(id) as id, rid from asset_versions group by rid) as q on q.rid = av1.rid set av1.uploaded_by = av2.audit_person, av1.uploaded_time = av2.audit_time where av2.id = q.id");
		$db->query(NULL, "delete from asset_versions where id not in (select active_vid from assets)");
		$db->query(NULL, "drop table assets");
		$db->query(NULL, "alter table asset_versions rename to assets");
		$db->query(NULL, "alter table assets drop id");
		$db->query(NULL, "alter table assets change rid id int unsigned");
		$db->query(NULL, "alter table assets add primary key (id)");
		$db->query(NULL, "alter table assets drop audit_person");
		$db->query(NULL, "alter table assets drop audit_time");
		$db->query(NULL, "alter table assets drop deleted");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function down(Kohana_Database $db)
	{
	}
}