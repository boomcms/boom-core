<?php

namespace BoomCMS\Core\Controllers\CMS;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Template;
use BoomCMS\Core\Page;
use BoomCMS\Core\Controller\Controller;

class Templates extends Controller
{
    /**
     *
     * @var Auth
     */
    private $auth;

    /**
     *
     * @var Template\Provider
     */
    private $provider;

    protected $viewPrefix = 'boom::templates';

    public function __construct(Auth $auth, Template\Provider $provider)
    {
        $this->auth = $auth;
        $this->provider = $provider;

        $this->authorization('manage_templates');
    }

    public function index()
    {
        $imported = $this->provider->createNew();
        $templates = $this->provider->findAll();

        return View::make("$this->viewPrefix/index", [
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

        return View::make("$this->viewPrefix/pages", [
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
