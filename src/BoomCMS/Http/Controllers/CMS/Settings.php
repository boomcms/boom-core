<?php

namespace BoomCMS\Http\Controllers\CMS;

use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Settings as SettingsStore;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;

class Settings extends Controller
{
    protected $view = 'boomcms::settings.index';

    protected $role = 'manage_settings';

    public function getIndex()
    {
        return View::make($this->view);
    }

    public function postIndex()
    {
        SettingsStore::replaceAll($this->request->input('settings'));

        return View::make($this->view, [
            'message' => Lang::get('boomcms::settings-manager._saved'),
        ]);
    }
}
