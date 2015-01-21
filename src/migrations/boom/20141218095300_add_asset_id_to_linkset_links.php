<?php

class Migration_Boom_20141218095300 extends Minion_Migration_Base
{
    public function up(Kohana_Database $db)
    {
            $db->query(null, "alter table chunk_linkset_links add asset_id int(10) unsigned references assets(id) on update cascade on delete set null");
    }

    public function down(Kohana_Database $db)
    {
    }
}