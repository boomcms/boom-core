<?php

class Migration_Boom_20140624123900 extends Minion_Migration_Base
{

    public function up(Kohana_Database $db)
    {
        $results = DB::select(array('t1.id', 't1id'), array('t2.id', 't2id'))
            ->from(array('tags', 't1'))
            ->join(array('tags', 't2'), 'inner')
            ->on('t1.name', '=', 't2.name')
            ->on('t1.id', '!=', 't2.id')
            ->where('t1.type', '!=', 't2.type')
            ->where('t1.type', '=', 1)
            ->execute()
            ->as_array();

        foreach ($results as $result)
        {
            try
            {
                DB::update('assets_tags')
                    ->set(array('tag_id' => $result['t2id']))
                    ->where('tag_id', '=', $result['t1id'])
                    ->execute();
            }
            catch (Exception $e) {}

            DB::delete('tags')
                ->where('id', '=', $result['t1id'])
                ->execute();
        }

        $results = DB::select(array('t1.id', 't1id'), array('t2.id', 't2id'))
            ->from(array('tags', 't1'))
            ->join(array('tags', 't2'), 'inner')
            ->on('t1.name', '=', 't2.name')
            ->on('t1.id', '!=', 't2.id')
            ->where('t1.slug_short', 'like', '%1')
            ->execute()
            ->as_array();

        foreach ($results as $result)
        {
            try
            {
                DB::update('assets_tags')
                    ->set(array('tag_id' => $result['t2id']))
                    ->where('tag_id', '=', $result['t1id'])
                    ->execute();
            }
            catch (Exception $e) {}

            DB::delete('tags')
                ->where('id', '=', $result['t1id'])
                ->execute();
        }

        $db->query(null, "alter table tags drop type");
        $db->query(null, "alter table tags drop index tag_name_type");
        $db->query(null, "create unique index tags_name on tags(name)");
    }

    public function down(Kohana_Database $db)
    {
    }
}