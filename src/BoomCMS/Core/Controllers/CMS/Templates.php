<?php

namespace BoomCMS\Core\Controllers\CMS;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Template;
use BoomCMS\Core\Page;
use BoomCMS\Core\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Templates extends Controller
{
    /**
     *
     * @var Auth
     */
    public $auth;

    /**
     *
     * @var Template\Provider
     */
    private $provider;

    protected $viewPrefix = 'boom::templates.';

    public function __construct(Auth $auth, Template\Provider $provider)
    {
        $this->auth = $auth;
        $this->provider = $provider;

        $this->authorization('manage_templates');
    }

    public function index(Template\Manager $manager)
    {
        $imported = $manager->createNew();
        $templates = $this->provider->findAll();

        return View::make($this->viewPrefix . 'index', [
            'imported' => $imported,        // The IDs of the templates which we've just added.
            'templates' => $templates,        // All the templates which are in the database.
        ]);
    }

    /**
     * Display a list of pages which use a given template.
     */
    public function pages($id, Page\Finder\Finder $finder)
    {
        $template = $this->provider->findById($id);

        if ( ! $template->loaded()) {
            throw new NotFoundHttpException();
        }

        $finder->addFilter(new Page\Finder\Template($template));
        $pages = $finder->findAll();

        return View::make($this->viewPrefix . '.pages', [
            'pages' => $pages,
        ]);
    }

    public function save(Request $request)
    {
        $post = $request->input();
        $templateIds = $post['templates'];

        foreach ($templateIds as $templateId) {
            $template = $this->provider->findById($templateId);
            $template
                ->setName($post["name-$templateId"])
                ->setFilename($post["filename-$templateId"])
                ->setDescription($post["description-$templateId"]);

            $this->provider->save($template);
        }
    }

    public function delete($id)
    {
        $this->provider->deleteById($id);
    }
}
