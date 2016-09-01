<?php

namespace BoomCMS\Http\Controllers\Asset;

use BoomCMS\Http\Controllers\Controller;
use Illuminate\View\View;

class AssetPickerController extends Controller
{
    /**
     * @var string
     */
    protected $viewPrefix = 'boomcms::assets.';

    /**
     * @return View
     */
    public function index()
    {
        return view($this->viewPrefix.'picker');
    }
}
