<?php

namespace BoomCMS\Observers;

use BoomCMS\Contracts\SingleSiteInterface;
use BoomCMS\Foundation\Database\Model;
use BoomCMS\Repositories\Site;
use BoomCMS\Routing\Router;

class SetSiteObserver
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Site
     */
    protected $site;

    /**
     * @param Guard $guard
     */
    public function __construct(Router $router, Site $site)
    {
        $this->router = $router;
        $this->site = $site;
    }

    /**
     * Set the site_id of the model.
     *
     * If a site is currently active then it is used, otherwise the default site is used.
     *
     * @param Model $model
     *
     * @return void
     */
    public function creating(Model $model)
    {
        if ($model instanceof SingleSiteInterface) {
            $site = $this->router->getActiveSite() ?: $this->site->findDefault();

            $model->{SingleSiteInterface::ATTR_SITE} = $site ? $site->getId() : null;
        }
    }
}
