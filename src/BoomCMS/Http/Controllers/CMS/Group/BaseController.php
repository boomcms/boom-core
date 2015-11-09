<?php

namespace BoomCMS\Http\Controllers\CMS\Group;

use BoomCMS\Support\Facades\Group;
use BoomCMS\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected $viewPrefix = 'boomcms::groups.';

    /**
     * @var Group\Group
     */
    public $group;

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->authorization('manage_people');
        $this->group = Group::findById($this->request->route()->getParameter('id'));
    }
}
