<?php

class Migration_Boom_20141218161300 extends Minion_Migration_Base
{
    public function up(Kohana_Database $db)
    {
            $db->query(null, "alter table chunk_slideshow_slides change title title varchar(255)");
    }

    public function down(Kohana_Database $db)
    {
    }
}