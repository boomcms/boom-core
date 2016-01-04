<?php

namespace BoomCMS\Http\Controllers\Group;

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

        $this->authorize('managePeople', $request);
        $this->group = $this->request->route()->getParameter('group');
    }
}
