<?php

namespace BoomCMS\Http\Controllers\CMS\Group;

use BoomCMS\Contracts\Models\Group;
use BoomCMS\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected $viewPrefix = 'boomcms::groups.';

    /**
     * @var Group
     */
    public $group;

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->authorization('manage_people');
        $this->group = $this->request->route()->getParameter('group');
    }
}
