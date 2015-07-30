<?php

namespace BoomCMS\Http\Controllers\CMS\Group;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Group;
use BoomCMS\Http\Controllers\Controller;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected $viewPrefix = 'boom::groups.';

    /**
     *
     * @var Group\Group
     */
    public $group;

    /**
	 *
	 * @var Group\Provider;
	 */
    protected $provider;

    public function __construct(Auth $auth, Group\Provider $provider, Request $request)
    {
        $this->auth = $auth;
        $this->provider = $provider;
        $this->request = $request;

        $this->authorization('manage_people');
        $this->group = $this->provider->findById($this->request->route()->getParameter('id'));
    }
}
