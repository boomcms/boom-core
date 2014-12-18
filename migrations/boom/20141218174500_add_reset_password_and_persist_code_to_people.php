<?php

class Migration_Boom_20141218174500 extends Minion_Migration_Base
{
    public function up(Kohana_Database $db)
    {
            $db->query(null, "drop table password_tokens");
            $db->query(null, "alter table people add reset_password_code varchar(255)");
            $db->query(null, "alter table people add persist_code varchar(255)");
    }

    public function down(Kohana_Database $db)
    {
    }
}