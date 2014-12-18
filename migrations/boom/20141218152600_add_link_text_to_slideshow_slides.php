<?php

class Migration_Boom_20141218152600 extends Minion_Migration_Base
{
    public function up(Kohana_Database $db)
    {
            $db->query(null, "alter table chunk_slideshow_slides add linktext text");
    }

    public function down(Kohana_Database $db)
    {
    }
}