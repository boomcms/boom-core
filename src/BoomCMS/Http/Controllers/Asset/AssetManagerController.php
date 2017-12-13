<?php

namespace BoomCMS\Http\Controllers\Asset;

use Illuminate\View\View;

class AssetManagerController extends AssetPickerController
{
    protected $role = 'manageAssets';

    public function __invoke(): View
    {
        return view($this->viewPrefix.'index');
    }
}
