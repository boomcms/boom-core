<?php

namespace BoomCMS\Http\Controllers\CMS;

use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Settings as SettingsStore;
use Illuminate\Support\Facades\Lang;

class Settings extends Controller
{
    protected $view = 'boomcms::settings.index';

    protected $role = 'manageSettings';

    public function getIndex()
    {
        return view($this->view);
    }

    public function postIndex()
    {
        SettingsStore::replaceAll($this->request->input('settings'));

        return view($this->view, [
            'message' => Lang::get('boomcms::settings-manager._saved'),
        ]);
    }
}
