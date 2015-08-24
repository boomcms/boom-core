<?php

class Migration_Boom_20141201153300 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
                $db->query(null, "alter table tags add `group` varchar(100)");
                $db->query(null, "alter table tags drop index tags_name");
                $db->query(null, "create unique index tags_group_name on tags(`group`, name)");
		$db->query(null, "alter table tags drop index tags_slug_long");

                $tags = DB::select('id', 'name')
                    ->from('tags')
                    ->where('name', 'like', '%/%')
                    ->execute();

                foreach ($tags as $tag) {

                        $parts = explode('/', $tag['name']);
                        $name = array_pop($parts);
                        $slug_short = URL::title($name);
                        $slug_long = '';

                        foreach ($parts as $part) {
                            $slug_long .= URL::title($part) . '/';
                        }

                        $slug_long .= $slug_short;
                        $group = count($parts) > 1? implode('/', $parts) :$parts[0];

                        DB::update('tags')
                            ->set(array(
                                'name' => $name,
                                'group' => $group,
                                'slug_short' => $slug_short,
                                'slug_long' => $slug_long
                            ))
                            ->where('id', '=', $tag['id'])
                            ->execute();
                }
        }

	public function down(Kohana_Database $db)
	{
	}
}
