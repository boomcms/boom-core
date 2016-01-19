<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Database\Models\Template;
use BoomCMS\Support\Facades\Template as TemplateFacade;
use BoomCMS\Support\Helpers;
use Illuminate\Support\Facades\Response;

class Templates extends Controller
{
    protected $viewPrefix = 'boomcms::templates.';

    protected $role = 'manageTemplates';

    public function index()
    {
        return view($this->viewPrefix.'index', [
            'templates' => TemplateFacade::findAll(),
        ]);
    }

    /**
     * Display a list of pages which use a given template.
     */
    public function pages(Template $template)
    {
        $pages = Helpers::getPages(['template' => $template, 'order' => 'title asc']);

        if ($this->request->route()->getParameter('format') !== 'csv') {
            return view($this->viewPrefix.'.pages', [
                'pages'    => $pages,
                'template' => $template,
            ]);
        }
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
    }

    public function save()
    {
        $post = $this->request->input();
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
