<?php

namespace BoomCMS\Http\Controllers;

class TemplateManagerController extends Controller
{
    protected $role = 'manageTemplates';

    public function index()
    {
        return view('boomcms::templates.template-manager');
    }
}
