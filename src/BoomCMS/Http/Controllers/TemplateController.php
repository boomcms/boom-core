<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Database\Models\Template;
use BoomCMS\Support\Facades\Template as TemplateFacade;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    protected $role = 'manageTemplates';

    public function index()
    {
        return view('boomcms::templates.template-manager');
    }

    public function update(Request $request, Template $template)
    {
        $template
            ->setName($request->input('name'))
            ->setFilename($request->input('filename'))
            ->setDescription($request->input('description'));

        return TemplateFacade::save($template);
    }

    public function destroy(Template $template)
    {
        TemplateFacade::delete($template);
    }
}
