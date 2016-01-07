<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Database\Models\Template;
use BoomCMS\Support\Facades\Template as TemplateFacade;
use BoomCMS\Support\Helpers;
use Illuminate\Http\Request;
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
    public function pages(Request $request, Template $template)
    {
        $pages = Helpers::getPages(['template' => $template, 'order' => 'title asc']);

        if ($request->route()->getParameter('format') !== 'csv') {
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

    public function save(Request $request)
    {
        $post = $request->input();
        $templates = TemplateFacade::find($post['templates']);

        foreach ($templates as $template) {
            $id = $template->getId();

            $template
                ->setName($post["name-$id"])
                ->setFilename($post["filename-$id"])
                ->setDescription($post["description-$id"]);

            TemplateFacade::save($template);
        }
    }

    public function delete(Template $template)
    {
        TemplateFacade::delete($template);
    }
}
