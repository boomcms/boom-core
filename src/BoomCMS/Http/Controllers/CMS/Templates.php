<?php

namespace BoomCMS\Http\Controllers\CMS;

use BoomCMS\Core\Page;
use BoomCMS\Core\Template\Manager as TemplateManager;
use BoomCMS\Database\Models\Template;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Template as TemplateFacade;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class Templates extends Controller
{
    protected $viewPrefix = 'boomcms::templates.';

    protected $role = 'manage_templates';

    public function index()
    {
        $manager = new TemplateManager(App::make('files'), TemplateFacade::getFacadeRoot());
        $manager->findAndInstallNewTemplates();

        return View::make($this->viewPrefix.'index', [
            'templates' => TemplateFacade::findAll(),
        ]);
    }

    /**
     * Display a list of pages which use a given template.
     */
    public function pages(Template $template)
    {
        $finder = new Page\Finder\Finder();
        $finder->addFilter(new Page\Finder\Template($template));
        $finder->setOrderBy('title', 'asc');
        $pages = $finder->findAll();

        if ($this->request->route()->getParameter('format') === 'csv') {
            $headers = [
                'Content-type'        => 'text/csv',
                'Content-Disposition' => "attachment; filename=pages_with_template_{$template->getFilename()}.csv",
            ];

            $callback = function () use ($pages) {
                $fh = fopen('php://output', 'w');

                fputcsv($fh, ['Title', 'URL', 'Visible?', 'Last edited']);

                foreach ($pages as $p) {
                    $data = [
                        'title'       => $p->getTitle(),
                        'url'         => (string) $p->url(),
                        'visible'     => $p->isVisible() ? 'Yes' : 'No',
                        'last_edited' => $p->getLastModified()->format('Y-m-d H:i:s'),
                    ];

                    fputcsv($fh, $data);
                }

                fclose($fh);
            };

            return Response::stream($callback, 200, $headers);
        } else {
            return View::make($this->viewPrefix.'.pages', [
                'pages'    => $pages,
                'template' => $template,
            ]);
        }
    }

    public function save(Request $request)
    {
        $post = $request->input();
        $templateIds = $post['templates'];

        foreach ($templateIds as $templateId) {
            $template = TemplateFacade::find($templateId);
            $template
                ->setName($post["name-$templateId"])
                ->setFilename($post["filename-$templateId"])
                ->setDescription($post["description-$templateId"]);

            TemplateFacade::save($template);
        }
    }

    public function delete(Template $template)
    {
        TemplateFacade::delete($template);
    }
}
