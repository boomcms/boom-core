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

    public function __invoke(): View
    {
        return view($this->viewPrefix.'picker');
    }
}
