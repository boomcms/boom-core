<?php

namespace BoomCMS\Core\Controllers\CMS;

use Boom\Template;
use Boom\Page;
use Boom\Controller\Controller;

class Templates extends Controller
{
    protected $viewDirectory = 'boom/templates';

    public function before()
    {
        parent::before();

        $this->authorization('manage_templates');
    }

    public function index()
    {
        $manager = new Template\Manager();
        $imported = $manager->createNew();

        $finder = new Template\Finder();
        $templates = $finder
            ->setOrderBy('name', 'asc')
            ->findAll();

        $this->template = View::factory("$this->viewDirectory/index", [
            'imported'        =>    $imported,        // The IDs of the templates which we've just added.
            'templates'    =>    $templates,        // All the templates which are in the database.
        ]);
    }

    /**
	 * Display a list of pages which use a given template.
	 * A template ID is given via the URL.
	 *
	 * @example	/cms/templates/pages/1
	 */
    public function pages()
    {
        $template = Template\Factory::byId($this->request->param('id'));

        $finder = new Page\Finder();
        $finder->addFilter(new Page\Finder\Filter\Template($template));
        $pages = $finder->findAll();

        $this->template = new View("$this->viewDirectory/pages", [
            'pages' => $pages,
        ]);
    }

    public function save()
    {
        $post = $this->request->input();
        $template_ids = $post['templates'];

        $errors = [];

        foreach ($template_ids as $template_id) {
            try {
                $template = ORM::factory('Template', $template_id)
                    ->values([
                        'name'        =>    $post["name-$template_id"],
                        'filename'        =>    $post["filename-$template_id"],
                        'description'    =>    $post["description-$template_id"],
                    ])
                    ->update();
            } catch (ORM_Validation_Exception $e) {
                $errors[] = $e->errors('models');
            }
        }
    }

    public function delete()
    {
        $template = Template\Factory::byId($this->request->param('id'));
        $template->delete();
    }
}
