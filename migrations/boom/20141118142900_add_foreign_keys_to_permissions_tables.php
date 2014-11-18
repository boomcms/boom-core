<?php

class Migration_Boom_20141118142900 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
                $db->query(null, "alter table group_roles engine=InnoDB");
                $db->query(null, "alter table groups engine=InnoDB");
                $db->query(null, "alter table people_groups engine=InnoDB");
                $db->query(null, "alter table people engine=InnoDB");
                $db->query(null, "alter table people_roles engine=InnoDB");
                $db->query(null, "alter table roles engine=InnoDB");

                $db->query(null, "alter table group_roles change role_id role_id smallint unsigned not null");
                $db->query(null, "alter table group_roles change group_id group_id smallint unsigned not null");
                $db->query(null, "alter table people_roles change role_id role_id smallint unsigned not null");
                $db->query(null, "alter table people_groups change person_id person_id mediumint unsigned not null");
                $db->query(null, "alter table people_roles change person_id person_id mediumint unsigned not null");
        
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