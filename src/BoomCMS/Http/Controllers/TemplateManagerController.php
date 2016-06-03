<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Database\Models\Template;

class TemplateManagerController extends Controller
{
    protected $role = 'manageTemplates';

    public function index()
    {
        return view('boomcms::templates.template-manager');
    }
}
