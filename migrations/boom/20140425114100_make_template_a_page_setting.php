<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20140425114100 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
		$db->query(null, "ALTER TABLE templates ENGINE=InnoDB;");
		$db->query(null, "alter table pages add template_id tinyint unsigned");
		$db->query(null, "ALTER TABLE pages ADD CONSTRAINT pages_template_id FOREIGN KEY (template_id) references templates(id) on update cascade on delete restrict");

		$pages = ORM::factory('Page')->find_all();
		foreach ($pages as $page)
		{
			$result = DB::select('template_id')
				->from('page_versions')
				->where('id', '=', $page->version()->id)
				->execute()
				->as_array();

			if (isset($result[0]))
			{
				$template = new Model_Template($result[0]['template_id']);

				if ($template->loaded())
				{
					$page->template_id = $result[0]['template_id'];
				}
				else
				{
					$page->template_id = 1;
				}

				$page->update();
			}
		}

		$db->query(null, "alter table page_versions drop template_id");
	}

	public function down(Kohana_Database $db)
	{
	}
}