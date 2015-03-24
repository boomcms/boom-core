<?php

class Migration_Boom_20141202161600 extends Minion_Migration_Base
{

    public function up(Kohana_Database $db)
    {
        $db->query(null, "delete assets_tags.* from assets_tags left join tags on assets_tags.tag_id = tags.id where tags.name is null");
        $db->query(null, "alter table assets_tags add tag varchar(50) not null");
        $db->query(null, "update assets_tags inner join tags on tag_id = tags.id set assets_tags.tag = tags.name where tags.name is not null and tags.id is not null");
        $db->query(null, "alter table assets_tags drop primary key");
        $db->query(null, "alter ignore table assets_tags add unique index assets_tags_tag_asset_id(tag, asset_id)");
        $db->query(null, "alter table chunk_tags add tag varchar(50) not null");
        $db->query(null, "update chunk_tags inner join assets_tags on chunk_tags.tag_id = assets_tags.tag_id set chunk_tags.tag = assets_tags.tag");
        $db->query(null, "alter table chunk_tags drop foreign key chunk_tags_ibfk_1");
        $db->query(null, "alter table chunk_tags drop tag_id");
        $db->query(null, "alter table assets_tags drop tag_id");
        $db->query(null, "alter table assets_tags drop index assets_tags_asset_id");
        $db->query(null, "delete tags.* from tags left join pages_tags on tags.id = pages_tags.tag_id where pages_tags.page_id is null");
    }

    public function down(Kohana_Database $db)
    {
    }
}