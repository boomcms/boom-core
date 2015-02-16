<?php

class Migration_Boom_20150216091800 extends Minion_Migration_Base
{
    public function up(Kohana_Database $db)
    {
            $db->query(null, "alter table tags drop slug_long");
    }

    public function down(Kohana_Database $db)
    {
    }
}