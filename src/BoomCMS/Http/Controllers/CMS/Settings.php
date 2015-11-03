<?php

namespace BoomCMS\Http\Controllers\CMS;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Settings as SettingsStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;

class Settings extends Controller
{
    protected $view;

    public function __construct(Request $request, Auth $auth)
    {
        $this->request = $request;
        $this->auth = $auth;

        $this->authorization('manage_settings');

        $this->view = View::make('boom::settings.index');
    }

    public function getIndex()
    {
        return $this->view;
    }

    public function postIndex()
    {        
        SettingsStore::replaceAll($this->request->input('settings'));

        return $this->view->with('message', Lang::get('boom::settings-manager._saved'));
    }
}
