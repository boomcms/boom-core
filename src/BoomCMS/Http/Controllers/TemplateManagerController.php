<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Database\Models\Template;
use BoomCMS\Support\Facades\Template as TemplateFacade;
use Illuminate\Http\Request;

class TemplateManagerController extends Controller
{
    protected $role = 'manageTemplates';

    public function index()
    {
        return view('boomcms::templates.template-manager');
    }
}
