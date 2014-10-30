<?php

class Migration_Boom_20141031102100 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
		$db->query(null, "delete group_roles.* from group_roles left join roles on group_roles.role_id = roles.id where roles.id is null");
		$db->query(null, "alter table group_roles add foreign key (role_id) references roles(id) on delete cascade on update cascade");

		$db->query(null, "delete group_roles.* from group_roles left join groups on group_roles.group_id = groups.id where groups.id is null");
		$db->query(null, "alter table group_roles add foreign key (group_id) references groups(id) on delete cascade on update cascade");

		$db->query(null, "delete people_groups.* from people_groups left join groups on people_groups.group_id = groups.id where groups.id is null");
		$db->query(null, "alter table people_groups add foreign key (group_id) references groups(id) on delete cascade on update cascade");

		$db->query(null, "delete people_groups.* from people_groups left join people on people_groups.person_id = people.id where people.id is null");
		$db->query(null, "alter table people_groups add foreign key (person_id) references people(id) on delete cascade on update cascade");

		$db->query(null, "delete people_roles.* from people_roles left join roles on people_roles.role_id = roles.id where roles.id is null");
		$db->query(null, "alter table people_roles add foreign key (role_id) references roles(id) on delete cascade on update cascade");

		$db->query(null, "delete people_roles.* from people_roles left join people on people_roles.person_id = people.id where people.id is null");
		$db->query(null, "alter table people_roles add foreign key (person_id) references people(id) on delete cascade on update cascade");
	}

	public function down(Kohana_Database $db)
	{
	}
}