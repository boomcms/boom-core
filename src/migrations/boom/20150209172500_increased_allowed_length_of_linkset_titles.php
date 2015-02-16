<?php

class Migration_Boom_20150209172500 extends Minion_Migration_Base
{
    public function up(Kohana_Database $db)
    {
            $db->query(null, "alter table chunk_linksets change title title varchar(255)");
    }

    public function down(Kohana_Database $db)
    {
    }
}