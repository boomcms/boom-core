<?php

class Task_Boom_Createrootpage extends Minion_Task
{
    protected $_options = [
        'template' => null,
        'uri' => '/',
    ];
    /**
	 * Execute the task
	 *
	 * @param array Config for the task
	 */
    protected function _execute(array $options)
    {
        $template_filename = $options['template'];
        $uri = $options['uri'];

        $template = new Model_Template(['filename' => $template_filename]);

        if ( ! $template->loaded()) {
            echo "No template exists with filename: $template_filename";

            return;
        }

        Database::instance()->begin();

        $page = ORM::factory('Page')
            ->values([
                'visible_from' => time(),
            ])
            ->set('id', 5)
            ->create();

        ORM::factory('Page_Version')
            ->values([
                'page_id'        =>    $page->id,
                'template_id'    =>    $template->id,
                'title'            =>    'Untitled',
            ])
            ->create();

        $page->mptt->id = $page->id;
        $page->mptt->make_root();

        ORM::factory('Page_URL')
            ->values([
                'location'        =>    $uri,
                'page_id'        =>    $page->id,
                'is_primary'    =>    true,
            ])
            ->create();

        Database::instance()->commit();

        echo "Root page created with URI $uri";
    }
}
