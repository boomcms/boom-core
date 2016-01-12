<?php

namespace BoomCMS\Support\Traits;

use BoomCMS\Contracts\Models\Site;
use BoomCMS\Database\Models\Site as SiteModel;

trait MultipleSites
{
    /**
     * @param Site $site
     *
     * @return $this
     */
    public function addSite(Site $site)
    {
        $this->sites()->attach($site);

        return $this;
    }

    /**
     * @param array $sites
     *
     * @return $this
     */
    public function addSites(array $sites)
    {
        foreach ($sites as $site) {
            $this->addSite($site);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getSites()
    {
        return $this->sites()
            ->orderBy(SiteModel::ATTR_NAME, 'asc')
            ->get();
    }

    /**
     * @param Site $site
     *
     * @return bool
     */
    public function hasSite(Site $site)
    {
        return $this->sites()
            ->where(SiteModel::ATTR_ID, '=', $site->getId())
            ->exists();
    }

    /**
     * @param Site $site
     *
     * @return $this
     */
    public function removeSite(Site $site)
    {
        $this->sites()->detach($site);

        return $this;
    }
}
