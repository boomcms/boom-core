<?php

class Migration_Boom_20140528141400 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
		$db->query(null, "alter table pages add feature_image_id int unsigned");

		$pages = ORM::factory('Page')->find_all();
		foreach ($pages as $page)
		{
			$result = DB::select('feature_image_id')
				->from('page_versions')
				->where('page_id', '=', $page->id)
				->order_by('id', 'desc')
				->limit(1)
				->execute();

			if (count($result) && $result[0]['feature_image_id'] > 0) {
				$page->feature_image_id = $result[0]['feature_image_id'];
				$page->update();
			}
		}

		$db->query(null, "alter table page_versions drop feature_image_id");
		$db->query(null, "alter table pages add foreign key pages_feature_image_id (feature_image_id) references assets(id) on update cascade on delete set null");
	}

	public function down(Kohana_Database $db)
	{
	}
}